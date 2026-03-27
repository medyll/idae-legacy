<?php
declare(strict_types=1);

/**
 * InputValidatorTest — Unit tests for InputValidator
 *
 * @package Idae\Tests\Unit
 * Date: 2026-03-27
 */

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;
use AppCommon\InputValidator;

class InputValidatorTest extends TestCase
{
    // =========================================================================
    // INT TESTS
    // =========================================================================

    public function testIntValidatesNumericString(): void
    {
        $this->assertEquals(42, InputValidator::int('42'));
    }

    public function testIntValidatesInteger(): void
    {
        $this->assertEquals(42, InputValidator::int(42));
    }

    public function testIntValidatesFloat(): void
    {
        $this->assertEquals(42, InputValidator::int(42.9));
    }

    public function testIntRespectsMinConstraint(): void
    {
        $this->assertEquals(5, InputValidator::int(5, 0, 10));
    }

    public function testIntRespectsMaxConstraint(): void
    {
        $this->assertEquals(10, InputValidator::int(10, 0, 10));
    }

    public function testIntThrowsOnNonNumeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::int('abc');
    }

    public function testIntThrowsOnBelowMin(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::int(5, 10, 20);
    }

    public function testIntThrowsOnAboveMax(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::int(25, 0, 20);
    }

    // =========================================================================
    // STRING TESTS
    // =========================================================================

    public function testStringTrimsWhitespace(): void
    {
        $this->assertEquals('hello', InputValidator::string('  hello  '));
    }

    public function testStringConvertsToString(): void
    {
        $this->assertEquals('42', InputValidator::string(42));
    }

    public function testStringAllowsEmptyByDefault(): void
    {
        $this->assertEquals('', InputValidator::string('', 0, 100, true));
    }

    public function testStringThrowsOnEmptyWhenNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::string('', 1, 100, false);
    }

    public function testStringRespectsMinLength(): void
    {
        $this->assertEquals('abc', InputValidator::string('abc', 3, 10));
    }

    public function testStringThrowsOnTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::string('ab', 3, 10);
    }

    public function testStringThrowsOnTooLong(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::string('abcdefghijk', 1, 5);
    }

    // =========================================================================
    // EMAIL TESTS
    // =========================================================================

    public function testEmailValidatesCorrectFormat(): void
    {
        $this->assertEquals('test@example.com', InputValidator::email('test@example.com'));
    }

    public function testEmailTrimsWhitespace(): void
    {
        $this->assertEquals('test@example.com', InputValidator::email('  test@example.com  '));
    }

    public function testEmailAllowsEmptyByDefault(): void
    {
        $this->assertEquals('', InputValidator::email('', true));
    }

    public function testEmailThrowsOnEmptyWhenNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::email('', false);
    }

    public function testEmailThrowsOnInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::email('not-an-email');
    }

    public function testEmailThrowsOnMissingAt(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::email('testexample.com');
    }

    // =========================================================================
    // URL TESTS
    // =========================================================================

    public function testUrlValidatesHttpUrl(): void
    {
        $this->assertEquals('http://example.com', InputValidator::url('http://example.com'));
    }

    public function testUrlValidatesHttpsUrl(): void
    {
        $this->assertEquals('https://example.com', InputValidator::url('https://example.com'));
    }

    public function testUrlAllowsEmptyByDefault(): void
    {
        $this->assertEquals('', InputValidator::url('', [], true));
    }

    public function testUrlThrowsOnEmptyWhenNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::url('', [], false);
    }

    public function testUrlThrowsOnInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::url('not-a-url');
    }

    public function testUrlThrowsOnDisallowedProtocol(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::url('ftp://example.com', ['http', 'https']);
    }

    // =========================================================================
    // BOOL TESTS
    // =========================================================================

    public function testBoolAcceptsTrueBoolean(): void
    {
        $this->assertTrue(InputValidator::bool(true));
    }

    public function testBoolAcceptsFalseBoolean(): void
    {
        $this->assertFalse(InputValidator::bool(false));
    }

    public function testBoolAcceptsNumericOne(): void
    {
        $this->assertTrue(InputValidator::bool(1));
    }

    public function testBoolAcceptsNumericZero(): void
    {
        $this->assertFalse(InputValidator::bool(0));
    }

    public function testBoolAcceptsTrueString(): void
    {
        $this->assertTrue(InputValidator::bool('true'));
        $this->assertTrue(InputValidator::bool('TRUE'));
        $this->assertTrue(InputValidator::bool('yes'));
        $this->assertTrue(InputValidator::bool('on'));
    }

    public function testBoolAcceptsFalseString(): void
    {
        $this->assertFalse(InputValidator::bool('false'));
        $this->assertFalse(InputValidator::bool('FALSE'));
        $this->assertFalse(InputValidator::bool('no'));
        $this->assertFalse(InputValidator::bool('off'));
    }

    public function testBoolStrictModeRequiresBoolean(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::bool('true', true);
    }

    // =========================================================================
    // DATE TESTS
    // =========================================================================

    public function testDateValidatesCorrectFormat(): void
    {
        $this->assertEquals('2026-03-27', InputValidator::date('2026-03-27'));
    }

    public function testDateValidatesCustomFormat(): void
    {
        $this->assertEquals('27/03/2026', InputValidator::date('27/03/2026', 'd/m/Y'));
    }

    public function testDateAllowsEmptyByDefault(): void
    {
        $this->assertEquals('', InputValidator::date('', 'Y-m-d', true));
    }

    public function testDateThrowsOnInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::date('not-a-date');
    }

    public function testDateThrowsOnWrongFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::date('2026/03/27', 'Y-m-d');
    }

    // =========================================================================
    // ALPHANUMERIC TESTS
    // =========================================================================

    public function testAlphanumericAcceptsLettersAndNumbers(): void
    {
        $this->assertEquals('abc123', InputValidator::alphanumeric('abc123'));
    }

    public function testAlphanumericWithUnderscore(): void
    {
        $this->assertEquals('abc_123', InputValidator::alphanumeric('abc_123', 1, 255, true));
    }

    public function testAlphanumericThrowsOnSpecialChars(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::alphanumeric('abc@123');
    }

    public function testAlphanumericThrowsOnSpaces(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::alphanumeric('abc 123');
    }

    // =========================================================================
    // ARRAY TESTS
    // =========================================================================

    public function testArrayValidatesArray(): void
    {
        $this->assertEquals([1, 2, 3], InputValidator::array([1, 2, 3]));
    }

    public function testArrayAppliesElementValidator(): void
    {
        $result = InputValidator::array(['1', '2', '3'], fn($v) => (int)$v);
        $this->assertEquals([1, 2, 3], $result);
    }

    public function testArrayThrowsOnNonArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::array('not-an-array');
    }

    public function testArrayRespectsMinCount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::array([1], null, 2, 5);
    }

    public function testArrayRespectsMaxCount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::array([1, 2, 3, 4, 5, 6], null, 0, 5);
    }

    // =========================================================================
    // WHITELIST TESTS
    // =========================================================================

    public function testWhitelistAcceptsAllowedValue(): void
    {
        $this->assertEquals('active', InputValidator::whitelist('active', ['active', 'inactive']));
    }

    public function testWhitelistAcceptsAllowedNumericValue(): void
    {
        $this->assertEquals(1, InputValidator::whitelist(1, [0, 1, 2]));
    }

    public function testWhitelistThrowsOnDisallowedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::whitelist('pending', ['active', 'inactive']);
    }

    public function testWhitelistStrictMode(): void
    {
        // In strict mode, integer 1 should match integer 1 in whitelist
        $this->assertSame(1, InputValidator::whitelist(1, [1, 2], true));
    }

    public function testWhitelistNonStrictMode(): void
    {
        $this->assertEquals('1', InputValidator::whitelist('1', [1, 2], false));
    }

    // =========================================================================
    // STRIP HTML TESTS
    // =========================================================================

    public function testStripHtmlRemovesTags(): void
    {
        $this->assertEquals('Hello World', InputValidator::stripHtml('<p>Hello <b>World</b></p>'));
    }

    public function testStripHtmlAllowsEmptyByDefault(): void
    {
        $this->assertEquals('', InputValidator::stripHtml('<p></p>', true));
    }

    public function testStripHtmlThrowsOnEmptyWhenNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::stripHtml('<p></p>', false);
    }

    // =========================================================================
    // FILENAME TESTS
    // =========================================================================

    public function testFilenameAcceptsValidName(): void
    {
        $this->assertEquals('document.pdf', InputValidator::filename('document.pdf'));
    }

    public function testFilenameThrowsOnPathTraversal(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::filename('../../../etc/passwd');
    }

    public function testFilenameThrowsOnSlash(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::filename('path/to/file.txt');
    }

    public function testFilenameThrowsOnBackslash(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::filename('C:\\Windows\\file.txt');
    }

    public function testFilenameThrowsOnNullByte(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        InputValidator::filename("file\0.txt");
    }
}
