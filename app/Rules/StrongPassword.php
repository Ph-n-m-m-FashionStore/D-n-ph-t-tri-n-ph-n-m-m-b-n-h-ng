<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use Closure;

class StrongPassword implements InvokableRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function __invoke(string $attribute, mixed $value, Closure $fail): void
    {
        if (! (is_string($value) && preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}$/', $value))) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     *
     * Note: InvokableRule uses the $fail callback; this helper method remains for
     * compatibility with any places that call ->message() directly.
     *
     * @return string
     */
    public function message()
    {
        return 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.';
    }
}
