<?php

namespace Leo980\Paginator;

class Paginator
{
    public const COLLAPSED = null;

    public function __construct(private int $neighbours = 3)
    {
        if ($neighbours < 0)
            throw new \InvalidArgumentException("\$neighbours could not be less than 0.");
    }

    public function __invoke(int $page, int $pages): array
    {
        if ($pages < 0)
            throw new \RangeException("\$pages could not be less than 0.");

        // Special consideration for 0 or 1 page
        if ($pages == 0 || $pages == 1)
            return [];

        // If there are pages, 1 <= $page <= $pages applies
        if ($pages < 1 || $page > $pages)
            throw new \RangeException("\$page should be within [1, \$pages]");

        $slice = [];

        // Generate the central slice,
        // make sure all items clamped within [0, $pages]
        $c0 = $page - $this->neighbours;
        $c1 = $page + $this->neighbours;

        for ($i = $c0; $i <= $c1; $i++)
            if ($i >= 1 && $i <= $pages)
                $slice[] = $i;

        // If slice does not contain first page or last page,
        // add them to the head or tail correspondingly
        if (!in_array(1, $slice, true))
            $slice = [...[1, self::COLLAPSED], ...$slice];

        if (!in_array($pages, $slice, true))
            $slice = [...$slice, ...[self::COLLAPSED, $pages]];

        // Remove unreasonable dots if exist ...
        // e.g. 1-...-2-3-4-5-6 => 1-2-3-4-5-6
        // or   1-...-3-4-5-6-7 => 1-2-3-4-5-6-7
        foreach ($slice as $i => $p) {
            if ($p == self::COLLAPSED && $slice[$i - 1] + 1 == $slice[$i + 1])
                unset($slice[$i]);

            if ($p == self::COLLAPSED && $slice[$i - 1] + 2 == $slice[$i + 1])
                $slice[$i] = $slice[$i - 1] + 1;
        }

        return array_values($slice);
    }
}