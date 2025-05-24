/**
 * Unified advanced socket app: fusion complète de appsocket.app.js et app_idaertys.app.js
 * - Gestion avancée des rooms, sessions, heartbeat, MongoDB, authorizations, etc.
 * - Toutes les routes HTTP et tous les événements socket des deux fichiers sont présents.
 */

const default_port = 3005;
const sessionMgm = (() => {
  const sessions = new Map();

  return {
    add: (sess) => {
      if (sess && sess.sessionId) sessions.set(sess.sessionId, sess);
    },
    getBySession: (sessionId) => {
      return sessions.get(sessionId) || null;
    },
    removeBySession: (sessionId) => {
      sessions.delete(sessionId);
    }
  };
})();

const { program } = require('commander');
program.option('--port <number>', 'port du serveur', default_port);
program.parse(process.argv);
const port = program.opts().port || process.env.PORT || default_port;


const fs = require("fs");
const http = require("http");
const socketio = require("socket.io");
const url = require("url");
const qs = require("qs");
const request = require("request");
const cookie = require("cookie");
const cookieParser = require("socket.io-cookie");
const mongo = require("mongodb");
const Server = mongo.Server;
const Db = mongo.Db;

var appcron = require("./appmodules/appcron.app.js");

http.globalAgent.maxSockets = Infinity;

const app = http.createServer(http_handler);
const io = socketio(app);

// --- MongoDB connection ---
const server2 = new Server("localhost", 27017, { auto_reconnect: true });
const socket_db = new Db("sitebase_sockets", server2, { safe: false });
open_socket_db();

// --- Authorization (cookie + PHPSESSID) ---
/* io.use(cookieParser); */
/* io.use(check_php_cookie); */
io.set("authorization", function (handshakeData, accept) {
  console.log("auth", handshakeData.headers);
  // check if there's a cookie header
  if (handshakeData.headers.cookie) {
    // if there is, parse the cookie
    handshakeData.cookie = cookie.parse(handshakeData.headers.cookie);
    cookie_obj = cookie.parse(handshakeData.headers.cookie);
    if (cookie_obj.PHPSESSID == "undefined") {
      console.log("Bad transmitted.");
      return accept("Bad transmitted.", false);
    }
    // }
  } else {
    return accept("No cookie transmitted.", false);
  }
  accept(null, true);
});

let socket_db_collection, socket_db_collection_site;

async function open_socket_db() {
  socket_db.open(function (err, db) {
    db.admin().authenticate("admin", "gwetme2011", function (err, result) {
      console.log("opening socket db", err, result);
      socket_db_collection = socket_db.collection("onLine");
      socket_db_collection_site = socket_db.collection("onLineSite");
    });
  });
}

function http_handler(req, res) {
  if (!req.url) return;
  if (req.url === "/favicon.ico") {
    res.writeHead(200, { "Content-Type": "image/x-icon" });
    res.end();
    return;
  }
  const path = url.parse(req.url).pathname;
  let fullBody = "";

  req.on("data", function (chunk) {
    fullBody += chunk.toString();
    if (fullBody.length > 1e6) req.connection.destroy();
  });

  switch (path) {
    case "/postScope":
      req.on("end", function () {
        const data = qs.parse(fullBody);
        let reloadVars = { scope: data.scope, value: data.value };
        if (data.vars) reloadVars.vars = qs.stringify(data.vars);
        if (data.scope && data.value) {
          io.sockets.emit("reloadScope", reloadVars);
        }
        res.writeHead(200, { "Content-Type": "text/html" });
        res.end();
      });
      break;
    case "/run":
      req.on("end", function () {
        const data = qs.parse(fullBody);
        data.vars = data.vars || "";
        data.options = data.options || {};
        data.vars.defer = "";
        const SESSID = data.SESSID || "";
        const PHPSESSID = data.PHPSESSID || "none";
        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN;
        const urlphp = "http://" + DOCUMENTDOMAIN + "/" + data.mdl + ".php";
        request.post(
          {
            url: urlphp,
            method: "POST",
            headers: {
              Cookie: "PHPSESSID=" + PHPSESSID + "; path=/",
              "content-type": "application/x-www-form-urlencoded",
            },
            body: qs.stringify(data.vars),
          },
          function (err, res2, body) {
            // Optionnel: callback ou log
          }
        );
      });
      break;
    case "/runModule":
      req.on("end", function () {
        const data = qs.parse(fullBody);
        const vars = data.vars || "";
        const mdl = data.mdl || "";
        const SESSID = data.SESSID || "";
        const PHPSESSID = data.PHPSESSID || "";
        const DOCUMENTDOMAIN =
          data.DOCUMENTDOMAIN || "app.destinationsreve.com";
        request.post(
          {
            uri: "http://" + DOCUMENTDOMAIN + "/mdl/" + mdl + ".php",
            headers: {
              Cookie: "PHPSESSID=" + PHPSESSID + "; path=/",
              "content-type": "application/x-www-form-urlencoded",
            },
            body: qs.stringify(vars),
          },
          function (err, res2, body) {
            io.sockets.emit("act_run", { body: body });
          }
        );
        res.writeHead(200, { "Content-Type": "text/html" });
        res.end();
      });
      break;
    case "/runForgetModule":
      req.on("end", function () {
        const data = qs.parse(fullBody);
        const vars = data.vars || "";
        const mdl = data.mdl || "";
        const SESSID = data.SESSID || "";
        const PHPSESSID = data.PHPSESSID || "";
        const DOCUMENTDOMAIN =
          data.DOCUMENTDOMAIN || "app.destinationsreve.com";
        request.post(
          {
            uri:
              "http://" +
              DOCUMENTDOMAIN +
              "/mdl/" +
              mdl +
              ".php?SESSID=" +
              SESSID +
              "&PHPSESSID=" +
              PHPSESSID,
            headers: { "content-type": "application/x-www-form-urlencoded" },
            body:
              "SESSID=" +
              SESSID +
              "&PHPSESSID=" +
              PHPSESSID +
              "&" +
              qs.stringify(vars),
          },
          function (err, res2, body) {
            if (res2 && typeof res2.resume === "function") res2.resume();
            console.log(body);
            io.sockets.emit("act_run", { body: body });
            res.writeHead(200, { "Content-Type": "text/html" });
            res.end("done runForgetModule");
          }
        );
      });
      break;
    case "/postReload":
      req.on("end", function () {
        const data = qs.parse(fullBody);
        res.writeHead(200, { "Content-Type": "text/html" });
        res.end();
        const DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "";
        let reloadVars = { module: data.module, value: data.value || "" };
        if (data.cmd && data.vars) {
          if (data.OWN) {
            io.sockets.to(data.OWN).emit("receive_cmd", data);
          } else {
            io.sockets.to(DOCUMENTDOMAIN).emit("receive_cmd", data);
          }
        }
        if (data.vars && typeof data.vars === "object")
          reloadVars.vars = qs.stringify(data.vars);
        if (data.module && data.value) {
          io.sockets.emit("reloadModule", reloadVars);
        }
      });
      break;
  }
}

// --- Namespace /fidel (migration de app3.app.js) ---
const keepFidel = io.of("/fidel").on("connection", function (socket) {
  socket.on("disconnect", function () {
    if (
      typeof socket_db_collection_site !== "undefined" &&
      socket_db_collection_site
    )
      socket_db_collection_site.remove({ socket: socket.id });
  });
  socket.on("grantSite", function (data) {
    data = data || {};
    data.ip = data.ip || "";
    data.ssid = data.ssid || "";
    data.host = data.host || "";
    data.uri = data.uri || "";
    let dadate = new Date();
    let time = dadate.getTime();
    if (
      typeof socket_db_collection_site !== "undefined" &&
      socket_db_collection_site
    ) {
      socket_db_collection_site.update(
        { socket: socket.id },
        {
          $set: {
            online: 1,
            ip: data.ip,
            ssid: data.ssid,
            host: data.host,
            uri: data.uri,
            time: Math.floor(time / 1000),
          },
        },
        { upsert: true }
      );
    }
  });
});

function check_php_cookie(socket, next) {
  let cookies = {};
  if (socket.request.headers.cookie) {
    cookies = socket.request.headers.cookie;
    if (typeof cookies === "string") cookies = cookie.parse(cookies);
    if (!cookies.PHPSESSID || cookies.PHPSESSID === "undefined") {
      return next(new Error("No valid PHPSESSID cookie transmitted."));
    }
    socket.PHPSESSID = cookies.PHPSESSID;
    // crossOriginIsolated.log('Valid PHPSESSID cookie transmitted.')
    next();
    authorization_ok(socket);
  } else {
    if (socket.handshake) {
      console.log("Echec .request", socket.request.headers);
    }
    next(new Error("not authorized"));
    return false;
  }
}

// --- MongoDB prefix by host ---
let prefix = "";

function sendGrantIn(socket) {
    socket.emit("ask_grantIn");
}

function authorization_ok(socket) {
  console.log("authorization_ok done");
  sendGrantIn(socket);
  return getDbHostPrefix(socket.request.headers.origin);

  function getDbHostPrefix(host) {
    if (host == null) return false;
    switch (host) {
      case "http://appmaw.mydde.fr":
      case "http://maw.idae.preprod.lan":
      case "http://appmaw-idaertys-preprod.mydde.fr":
        prefix = "maw";
        break;
      case "http://appcrfr.mydde.fr":
      case "http://appcrfr.idaertys-preprod.mydde.fr":
      case "http://crfr.idae.preprod.lan":
        prefix = "crfr";
        break;
      case "http://idaertys.mydde.fr":
        prefix = "idaenext";
        break;
      case "http://leasys.idae.preprod.lan":
      case "http://idaertys-preprod.mydde.fr":
        prefix = "idaenext";
        break;
      case "http://tactac.idae.preprod.lan":
      case "http://tactac_idae.preprod.mydde.fr":
        prefix = "tactac";
        break;
      case "http://idae.io.idae.preprod.lan":
        prefix = "idae_io";
        break;
      case "http://blog.idae.preprod.lan":
        prefix = "appblog";
        break;
    }

    console.log([host, prefix]);
    return [host, prefix];
  }
}

function build_header(data) {
  if (!data.DOCUMENTDOMAIN) return "";
  data.vars = data.vars || "";
  let SESSID = data.SESSID || "";
  let PHPSESSID = data.PHPSESSID || "";
  let DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "appgem.destinationsreve.com";
  return {
    Cookie: "PHPSESSID=" + PHPSESSID + "; path=/",
    "content-type": "application/x-www-form-urlencoded",
  };
}

function build_vars(data) {
  if (!data.DOCUMENTDOMAIN) return "";
  data.vars = data.vars || "";
  return data.vars;
}

function socket_handlers() {
  io.on("connection", function (socket) {
    // Join rooms by PHPSESSID
    if (socket.PHPSESSID) {
      socket.join(socket.PHPSESSID);
    }
    // Join by host if possible
    if (
      socket.handshake &&
      socket.handshake.headers &&
      socket.handshake.headers.host
    ) {
      socket.join(socket.handshake.headers.host);
    }

    json_cookie = cookie.parse(socket.handshake.headers.cookie);
    if (json_cookie.PHPSESSID) {
      socket.join(json_cookie.PHPSESSID);

      socket.PHPSESSID = json_cookie.PHPSESSID;
      socket.cookie_string = socket.handshake.headers.cookie;
    }
    // Heartbeat
    const sender = setInterval(function () {
      socket.emit("message", new Date().getTime());
      socket.emit("heartbeat_app", new Date().getTime());
    }, 10000);

    // Disconnect
    socket.on("disconnect", function () {
      clearInterval(sender);
      let session = sessionMgm.getBySession(socket.id);
      if (session && session.SESSID) {
        if (socket_db_collection)
          socket_db_collection.update(
            { idagent: eval(session.SESSID) },
            { $set: { online: 0 } },
            { upsert: true }
          );
        sessionMgm.removeBySession(socket.id);
      }
    });
    // grantIn
    socket.on("grantIn", function (data, fn) {
      var sess = new Object();
      console.log("socket on grantin ", data);
      sess.sessionId = socket.id;
      sess.DOCUMENTDOMAIN = data.DOCUMENTDOMAIN;
      sess.SESSID = data.SESSID;
      sess.PHPSESSID = data.PHPSESSID;

      if (data.DOCUMENTDOMAIN) {
        socket.join(data.DOCUMENTDOMAIN);
      }
      //
      io.sockets.to(data.DOCUMENTDOMAIN).send("user connected");
      //
      sessionMgm.add(sess);
      /*       if (data.IDAGENT) {
        socket.idagent = data.IDAGENT;
        socket.join(data.DOCUMENTDOMAIN + "IDAGENT" + data.IDAGENT);
      } */
      if (data.SESSID) {
        if (fn) fn("woot");
        socket.emit("notify", data);
        /* socket.broadcast.emit("reloadModule", {
          module: "gadget/onliveGadget",
          value: "*",
        }); */
      }
    });
    // dispatcher
    socket.on("dispatch_cmd", function (data) {
      io.sockets.emit(data.cmd, { vars: data });
      if (data.dispatch_to) {
        io.sockets
          .to(data.dispatch_to)
          .emit("receive_cmd", { cmd: data.cmd, vars: data.dispatch_vars });
      } else {
        io.sockets
          .to(data.DOCUMENTDOMAIN)
          .emit("receive_cmd", { cmd: data.cmd, vars: data.dispatch_vars });
      }
    });
    socket.on("reloadModule", function (data) {
      socket.broadcast.emit("reloadModule", data);
    });
    socket.on("reloadScope", function (data) {
      if (!data) return;
      if (!data.scope) return;
      let reloadVars = { scope: data.scope, value: data.value };
      if (data.vars) reloadVars.vars = qs.stringify(data.vars);
      if (data.scope && data.value) {
        io.sockets.emit("reloadScope", reloadVars);
      }
    });
    socket.on("loadModule", function (data, func) {
      let fn = func || null;
      let vars = data.vars || "";
      let title = data.title || "";
      let mdl = data.mdl || "";
      let SESSID = data.SESSID || "";
      let PHPSESSID = data.PHPSESSID || "";
      let DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "app.destinationsreve.com";
      if (DOCUMENTDOMAIN) {
        socket.join(DOCUMENTDOMAIN);
      }
      request.post(
        {
          uri:
            "http://" +
            DOCUMENTDOMAIN +
            "/mdl/" +
            mdl +
            ".php?SESSID=" +
            SESSID,
          headers: build_header(data),
          body: qs.stringify(vars),
        },
        function (err, res, body) {
          socket.emit("loadModule", {
            body: body,
            vars: vars,
            mdl: mdl,
            title: title,
          });
        }
      );
    });
    socket.on("socketModule", function (data, fun) {
      let fn = fun || null;
      data.vars = data.vars || "";
      data.options = data.options || {};
      data.vars.defer = "";
      let SESSID = data.SESSID || "";
      let PHPSESSID = data.PHPSESSID || "";
      let DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "appgem.destinationsreve.com";
      if (DOCUMENTDOMAIN) {
        socket.join(DOCUMENTDOMAIN);
      }
      let urlphp = "http://" + DOCUMENTDOMAIN + "/mdl/" + data.file + ".php";
      request.post(
        {
          url: urlphp,
          method: "POST",
          headers: build_header(data),
          body: data.vars,
        },
        function (err, res, body) {
          socket.emit("socketModule", { body: body, out: data });
          if (fn) fn({ body: body, data: data });
        }
      );
    });
    socket.on("upd_data", function (data) {
      let vars = data.vars || "";
      let title = data.title || "";
      let mdl = data.mdl || "";
      let SESSID = data.SESSID || "";
      let PHPSESSID = data.PHPSESSID || "";
      let DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "app.destinationsreve.com";
      request.post(
        {
          uri: "http://" + DOCUMENTDOMAIN + "/services/json_data_table_row.php",
          headers: build_header(data),
          body: qs.stringify(vars),
        },
        function (err, res, body) {
          io.sockets.emit("upd_data", {
            body: body,
            vars: vars,
            mdl: mdl,
            title: title,
          });
        }
      );
    });
    socket.on("get_data", function (data, options, fn) {
      let vars = data.vars || "";
      let mdl = data.mdl || "";
      options = options || {};
      let SESSID = data.SESSID || "";
      let PHPSESSID = data.PHPSESSID || "";
      let DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "app.destinationsreve.com";
      let directory = data.directory ? data.directory : "services";
      let extension = data.extension ? data.extension : "php";
      let urlphp =
        "http://" +
        DOCUMENTDOMAIN +
        "/" +
        directory +
        "/" +
        mdl +
        "." +
        extension;
 

      let j = request.jar();
      let soc_cook = socket.cookie_string;
      if (
        soc_cook !== undefined &&
        typeof soc_cook === "string" &&
        socket.PHPSESSID !== undefined
      ) {
        request.get(
          {
            url: urlphp,
            jar: j,
            method: "GET",
            headers: build_header(data),
            qs: vars,
          },
          function (err, res, body) {
            fn(body, options);
          }
        );
      } else {
        request.get(
          {
            url: urlphp,
            method: "GET",
            headers: { "content-type": "application/x-www-form-urlencoded" },
            qs: vars,
          },
          function (err, res, body) {
            fn(body, options);
          }
        );
      }
    });

    socket.on("runModule", function (data) {
      data.vars = data.vars || "";
      data.options = data.options || {};
      data.vars.defer = "";
      let SESSID = data.SESSID || "";
      let PHPSESSID = data.PHPSESSID || "";
      let DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || "appgem.destinationsreve.com";
      let urlphp = "http://" + DOCUMENTDOMAIN + "/" + data.file + ".php";
      let key = SESSID + data.file + data.vars;
      request.post(
        {
          url: urlphp,
          method: "POST",
          headers: build_header(data),
          body: data.vars,
        },
        function (err, res, body) {
          // Optionnel: callback ou log
        }
      );
    });
  });
}

const init_app = function (port) {
  app.listen(port);
  socket_handlers();
};

module.exports = {
  init_app: init_app,
  socket_start: function (port) {
    app.listen(port);
    socket_handlers();
  },
  io: io,
  keepFidel,
};

// Démarrage automatique si appelé directement
if (require.main === module) {
  app.listen(port);
  socket_handlers();
  appcron.cron_start();
  console.log("Advanced socket app started on port " + port);
}
