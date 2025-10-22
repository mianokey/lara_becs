<?php

function getStatusColor($status)
{
    return match($status) {
        'planning' => 'bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs',
        'active' => 'bg-green-100 text-green-800 px-2 py-1 rounded text-xs',
        'on_hold' => 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs',
        'completed' => 'bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs',
        default => 'bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs',
    };
}
