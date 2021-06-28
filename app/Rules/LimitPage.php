<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LimitPage implements Rule
{
    private $itemsQuantity;
    private $maxPage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($itemsQuantity)
    {
        $this->itemsQuantity = $itemsQuantity;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $itemsPerPage = config('api-pagination.items_per_page', 5);
        $this->maxPage = (int) ceil($this->itemsQuantity / $itemsPerPage);
        return $value <= $this->maxPage;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.lte.numeric', [
            'value' => $this->maxPage,
        ]);
    }
}
