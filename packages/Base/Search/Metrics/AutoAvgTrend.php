<?php

declare(strict_types=1);

namespace Sigmie\Base\Search\Metrics;

use Sigmie\Base\Contracts\Aggregation;
use Sigmie\Base\Contracts\ToRaw;
use Sigmie\Base\Search\Aggregations\Bucket\DateHistogram;
use Sigmie\Base\Search\Aggregations\Enums\CalendarInterval;
use Sigmie\Base\Search\Aggregations\Metrics\Metric;
use Sigmie\Base\Search\Aggregations\Metrics\Sum;
use Sigmie\Base\Search\Aggs;

class AutoAvgTrend extends AutoTrend
{
    protected function aggregation(Aggs $aggs): Metric
    {
        $field = $aggs->avg($this->trendName, $this->metricField);

        return $field;
    }
}
