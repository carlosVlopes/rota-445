<?php

namespace App\Enums;

enum ProductOptionType: string
{
    case Toggle = 'toggle';
    case Select = 'select';
    case Extra = 'extra';
    case Text = 'text';
}
