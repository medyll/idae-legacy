<?php

require_once('hprose/Hprose.php');

class Chat {
    private $gens = array();
    private $messages = array();
    private $getMessage = array();
    private $getUpdateUsers = array();
    private $maybeOffline = array();
    private $timer = null;

    private function messageGenerator($who) {
        while (true) {
            $message = yield;
            if ($message === null) {
                break;
            }
            if (isset($this->getMessage[$who])) {
                $getMessage = $this->getMessage[$who];
                unset($this->getMessage[$who]);
                swoole_timer_clear($getMessage->timer);
                $oldMessage = '';
                if (isset($this->messages[$who])) {
                    $oldMessage = $this->messages[$who];
                    unset($this->messages[$who]);
                }
                $getMessage->completer->complete($oldMessage . $message);
            }
            else {
                if (isset($this->messages[$who])) {
                    $this->messages[$who] .= $message;
                }
                else {
                    $this->messages[$who] = $message;
                }
            }
        }
    }

    private function sendUsers() {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if (isset($this->getUpdateUsers[$user])) {
                $getUpdateUsers = $this->getUpdateUsers[$user];
                unset($this->getUpdateUsers[$user]);
                swoole_timer_clear($getUpdateUsers->timer);
                $getUpdateUsers->completer->complete($users);
            }
        }
    }

    private function online($who) {
        if (!isset($this->gens[$who])) {
            $this->gens[$who] = $this->messageGenerator($who);
            $this->sendUsers();
            $this->broadcast($who, $who . " is online.");
        }
        if ($this->timer == null) {
            $this->timer = swoole_timer_add(1000, function() {
                $users = $this->getAllUsers();
                foreach ($users as $user) {
                    if (!isset($this->getMessage[$user]) &&
                        !isset($this->getUpdateUsers[$user])) {
                        if (!isset($this->maybeOffline[$user])) {
                            $this->maybeOffline[$user] = true;
                        }
                        else {
                            $this->offline($user);
                        }
                    }
                    else {
                        if (isset($this->maybeOffline[$user])) {
                            unset($this->maybeOffline[$user]);
                        }
                    }
                }
            });
        }
    }

    private function offline($who) {
        if (isset($this->gens[$who])) {
            $this->broadcast($who, $who . " is offline.");
            $gen = $this->gens[$who];
            unset($this->gens[$who]);
            unset($this->maybeOffline[$who]);
            unset($this->messages[$who]);
            $gen->send(null);
            $this->sendUsers();
        }
    }

    public function getAllUsers() {
        return array_keys($this->gens);
    }

    public function getUpdateUsers($who) {
        $this->online($who);
        $getUpdateUsers = new StdClass();
        $completer = new HproseCompleter();
        $getUpdateUsers->completer = $completer;
        $getUpdateUsers->timer = swoole_timer_after(29000, function() use ($who, $completer) {
            if (isset($this->getUpdateUsers[$who])) {
                unset($this->getUpdateUsers[$who]);
                $completer->complete($this->getAllUsers());
            }
        });
        $this->getUpdateUsers[$who] = $getUpdateUsers;
        return $completer->future();
    }

    public function getMessage($who) {
        $this->online($who);
        if (isset($this->messages[$who])) {
            $message = $this->messages[$who];
            unset($this->messages[$who]);
            return $message;
        }
        $getMessage = new StdClass();
        $completer = new HproseCompleter();
        $getMessage->completer = $completer;
        $getMessage->timer = swoole_timer_after(30000, function() use ($who, $completer) {
            if (isset($this->getMessage[$who])) {
                unset($this->getMessage[$who]);
                $completer->complete(null);
            }
        });
        $this->getMessage[$who] = $getMessage;
        return $completer->future();
    }

    public function sendMessage($from, $to, $message) {
        $this->online($from);
        if (!isset($this->gens[$to])) {
            return $to . "is offline.";
        }
        $this->gens[$to]->send($from . " talk to me: " . $message . "\r\n");
        $this->gens[$from]->send("I talk to " . $to . ": " . $message . "\r\n");
    }

    public function broadcast($from, $message) {
        $this->online($from);
        foreach ($this->gens as $gen) {
            $gen->send($from . " said: " . $message . "\r\n");
        }
    }
}

$server = new HproseSwooleServer("ws://0.0.0.0:8080", SWOOLE_BASE);
$server->setErrorTypes(E_ALL);
$server->setDebugEnabled(true);
$server->setCrossDomainEnabled(true);
$server->add(new Chat());
$server->start();
