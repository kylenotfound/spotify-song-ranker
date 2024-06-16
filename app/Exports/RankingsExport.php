<?php

namespace App\Exports;

use App\Exports\SongExport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RankingsExport implements WithMultipleSheets
{
    public function __construct(private Collection $rankings){}

    /**
    * @return array
    */
    public function sheets(): array
    {
        $sheets = [];

        foreach($this->rankings as $ranking) {
            $sheets[] = new SongExport($ranking->songs, $ranking->name);
        }

        return $sheets;
    }
}
