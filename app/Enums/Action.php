<?php

namespace App\Enums;

enum Action: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}