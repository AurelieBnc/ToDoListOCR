<?php

namespace App\EnumTodo;

enum TaskStatus: string
{
    public const STATUS_IS_DONE = 'isDone';
    public const STATUS_TODO = 'todo';

    case IsDone = self::STATUS_IS_DONE;
    case Todo = self::STATUS_TODO;
}
