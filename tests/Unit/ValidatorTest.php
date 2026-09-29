<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Exceptions\ValidationException;
use StarterKit\Mvc\Http\Validation\Validator;
use StarterKit\Mvc\Rules\Rule;

final class ValidatorTest extends TestCase
{
    public function testValidatedFieldsAreAllowlisted(): void
    {
        $validated = (new Validator())->validate(['name' => 'Ada', 'is_admin' => true], ['name' => ['required', 'string', 'max:20']]);
        self::assertSame(['name' => 'Ada'], $validated);
    }

    public function testInvalidValuesProduceFieldErrors(): void
    {
        try {
            (new Validator())->validate(['enabled' => 'yes'], ['enabled' => ['required', 'boolean']]);
            self::fail('Validation should fail.');
        } catch (ValidationException $exception) {
            self::assertArrayHasKey('enabled', $exception->errors());
        }
    }

    public function testUnicodeLengthUsesCharacters(): void
    {
        $validator = new Validator();
        self::assertSame(
            ['name' => str_repeat('آ', 120)],
            $validator->validate(['name' => str_repeat('آ', 120)], ['name' => ['required', 'string', 'max:120']])
        );
        $this->expectException(ValidationException::class);
        $validator->validate(['name' => str_repeat('آ', 121)], ['name' => ['required', 'string', 'max:120']]);
    }

    public function testCustomRuleCanRejectAValue(): void
    {
        $rule = new class implements Rule {
            public function passes(mixed $value): bool { return $value === 'approved'; }
            public function message(): string { return 'Approval is required.'; }
        };
        try {
            (new Validator())->validate(['status' => 'pending'], ['status' => [$rule]]);
            self::fail('Custom rule should fail.');
        } catch (ValidationException $exception) {
            self::assertSame(['Approval is required.'], $exception->errors()['status']);
        }
    }
}
