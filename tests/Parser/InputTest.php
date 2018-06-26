<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Parser;

use PHPStan\Testing\TestCase;
use Svoboda\PsrRouter\Parser\Input;
use Svoboda\PsrRouter\Parser\UnexpectedChar;

class InputTest extends TestCase
{
    public function test_peek_returns_character_without_consuming()
    {
        $input = new Input("abc");

        self::assertEquals("a", $input->peek());
        self::assertEquals("a", $input->peek());
    }

    public function test_peek_returns_eof_when_input_empty()
    {
        $input = new Input("");

        self::assertEquals(Input::END, $input->peek());
    }

    public function test_take_returns_characters_in_correct_order()
    {
        $input = new Input("abc");

        self::assertEquals("a", $input->take());
        self::assertEquals("b", $input->take());
        self::assertEquals("c", $input->take());
    }

    public function test_expect_fails_on_unexpected_character()
    {
        $input = new Input("abc");

        $input->expect("a");
        $input->expect("b");

        $this->expectException(UnexpectedChar::class);

        $input->expect("d");
    }

    public function test_take_all_while_can_take_whole_string()
    {
        $input = new Input("abc");

        self::assertEquals("abc", $input->takeAllAlphaNumUntil(Input::END));
    }

    public function test_take_all_while_stops_at_bad_character()
    {
        $input = new Input("ab_c");

        self::assertEquals("ab", $input->takeAllAlphaNumUntil("_"));
    }

    public function test_take_all_while_can_return_empty_string()
    {
        $input = new Input("_abc");

        self::assertEquals("", $input->takeAllAlphaNumUntil("_"));
    }

    public function test_take_all_until_can_take_whole_string()
    {
        $input = new Input("abc");

        self::assertEquals("abc", $input->takeAllUntil("d"));
    }

    public function test_take_all_until_stops_at_bad_character()
    {
        $input = new Input("abc");

        self::assertEquals("ab", $input->takeAllUntil("c"));
    }

    public function test_take_all_until_can_return_empty_string()
    {
        $input = new Input("abc");

        self::assertEquals("", $input->takeAllUntil("a"));
    }

    public function test_empty_string_is_at_end()
    {
        $input = new Input("");

        self::assertTrue($input->atEnd());
    }

    public function test_non_empty_string_not_at_end()
    {
        $input = new Input("abc");

        self::assertFalse($input->atEnd());
    }

    public function test_consumed_string_is_at_end()
    {
        $input = new Input("abc");

        $input->take();
        $input->take();
        $input->take();

        self::assertTrue($input->atEnd());
    }

    public function test_index_initially_returns_zero()
    {
        $input = new Input("abc");

        self::assertEquals(0, $input->getIndex());
    }

    public function test_index_returns_correct_number()
    {
        $input = new Input("abc");

        $input->take();
        $input->take();

        self::assertEquals(2, $input->getIndex());
    }

    public function test_index_stops_after_the_last_character()
    {
        $input = new Input("abc");

        $input->take();
        $input->take();
        $input->take();
        $input->take();
        $input->take();
        $input->take();

        self::assertEquals(3, $input->getIndex());
    }
}
