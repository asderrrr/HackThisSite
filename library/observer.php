<?php
/**
Copyright (c) 2010, HackThisSite.org
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the HackThisSite.org nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS ``AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
* Authors:
*   Thetan ( Joseph Moniz )
**/

class Observer
{
    private static $instance;
    private $events;

    // singleton
    private function __construct($events = false)
    {
        if (!$events) return;
        $this->listen($events);
    }

    public static function singleton($events = false)
    {
        if (!isset(self::$instance))
        {
            $thisClass = __CLASS__;
            self::$instance = new $thisClass($events);
        }
        return self::$instance;
    }

    public function trigger($hook)
    {
        if (empty($this->events[$hook])) return;
        foreach ($this->events[$hook] as $h)
        {
            $obj = new $h();
        }
    }

    private function listen($events)
    {
        foreach ($events as $x => $eventset)
        {
            foreach ($eventset as $hook)
            {
                $this->events[$x][] = "hook_{$hook}";
            }
        }
    }

    public function __clone()
    {
        die("Error, can not be cloned");
    }
}
