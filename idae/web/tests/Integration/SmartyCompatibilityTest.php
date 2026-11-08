<?php
declare(strict_types=1);

/**
 * Verify template rendering contracts when updating the legacy Smarty dependency.
 *
 * @package Idae\Tests\Integration
 * @date 2026-11-08
 */
namespace Idae\Tests\Integration;

use Idae\Tests\TestCase;

class SmartyCompatibilityTest extends TestCase
{
    private function renderer(): \Smarty
    {
        $smarty = new \Smarty();
        $compileDir = sys_get_temp_dir() . '/idae-smarty-contract-cache';
        if (!is_dir($compileDir)) {
            mkdir($compileDir, 0700, true);
        }
        $smarty->setCompileDir($compileDir);
        return $smarty;
    }

    public function testHtmlEscapingPreservesUntrustedText(): void
    {
        $smarty = $this->renderer();
        $smarty->assign('value', '<script>alert("x")</script>');
        $this->assertSame(
            '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;',
            $smarty->fetch('string:{$value|escape:"html"}')
        );
    }

    public function testRecordLoopsPreserveFieldValues(): void
    {
        $smarty = $this->renderer();
        $smarty->assign('rows', [['id' => 1, 'name' => 'Alpha'], ['id' => 2, 'name' => 'Beta']]);
        $this->assertSame('1:Alpha;2:Beta;', $smarty->fetch('string:{foreach $rows as $row}{$row.id}:{$row.name};{/foreach}'));
    }

    public function testCompiledTemplateInheritanceResolvesOverrides(): void
    {
        $smarty = $this->renderer();
        $templateDir = sys_get_temp_dir() . '/idae-smarty-contract-' . bin2hex(random_bytes(8));
        mkdir($templateDir, 0700);
        try {
            file_put_contents($templateDir . '/base.tpl', '{block name=body}base{/block}');
            file_put_contents($templateDir . '/child.tpl', '{extends file="base.tpl"}{block name=body}{$value}{/block}');
            $smarty->setTemplateDir($templateDir);
            $smarty->assign('value', 'override');
            $this->assertSame('override', $smarty->fetch('child.tpl'));
        } finally {
            unlink($templateDir . '/child.tpl');
            unlink($templateDir . '/base.tpl');
            rmdir($templateDir);
        }
    }
}
