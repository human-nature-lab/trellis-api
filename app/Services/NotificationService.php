<?php

namespace app\Services;



class NotificationService
{
    public static function addNewNotification($request, $message, $class, $status, $dismissable = null)
    {
        $request->session()->flash('message', $message);
        $request->session()->flash('alert-class', $class);
        $request->session()->flash('status', $status);
        $request->session()->flash('dismissable', $dismissable);
    }

}