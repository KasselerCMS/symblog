<?php

namespace Kasseler\Symblog\Twig\Extensions;

class KasselerBlogExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array('created_ago' => new \Twig_Filter_Method($this, 'createdAgo'),);
    }

    public function createdAgo(\DateTime $dateTime)
    {
        $delta = time()-$dateTime->getTimestamp();
        if ($delta < 0) {
            throw new \InvalidArgumentException("createdAgo is unable to handle dates in the future");
        }

        if ($delta < 60) {
            $time = $delta;
            $duration = $time." second".(($time > 1) ? "s" : "")." ago";
        } else if($delta <= 3600) {
            $time = floor($delta/60);
            $duration = $time." minute".(($time > 1) ? "s" : "")." ago";
        } else if($delta <= 86400) {
            $time = floor($delta/3600);
            $duration = $time." hour".(($time > 1) ? "s" : "")." ago";
        } else {
            $time = floor($delta/86400);
            $duration = $time." day".(($time > 1) ? "s" : "")." ago";
        }

        return $duration;
    }

    public function getName()
    {
        return 'symblog_extension';
    }
}
