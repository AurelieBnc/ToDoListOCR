<?php

namespace App\EnumTodo;

enum TaskStatus: string
{
    case IsDone = 'isDone';
    case Todo = 'todo';
    
}
