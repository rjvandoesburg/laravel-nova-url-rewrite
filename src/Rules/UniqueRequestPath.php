<?php

namespace Rjvandoesburg\NovaUrlRewrite\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueRequestPath implements Rule
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var bool
     */
    protected $updating;

    /**
     * UniqueRequestPath constructor.
     *
     * @param  string  $table
     * @param  bool  $updating
     */
    public function __construct(string $table, $updating = false)
    {
        $this->updating = $updating;
        $this->table = $table;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $value = '/'.ltrim($value, '/');
        $resourceId = request()->route('resourceId');

        $query = \DB::table($this->table)->where($attribute, $value);

        if ($this->updating) {
            $query->where('id', '!=', $resourceId);
        }

        return ! $query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message():string
    {
        return 'The :attribute has already been taken.';
    }
}
