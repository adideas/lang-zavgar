<?php

namespace App\classes\DiffText;

class DiffText
{
    // https://stackoverflow.com/questions/321294/highlight-the-difference-between-two-strings-in-php

    // Вот короткая функция, которую вы можете использовать для сравнения двух массивов. Он реализует алгоритм LCS
    // https://en.wikipedia.org/wiki/Longest_common_subsequence_problem

    public static function text($from_text, $to_text)
    {
        // Он генерирует два массива:
        //
        // values array: список элементов в том виде, в котором они появляются в diff.
        // массив масок: содержит числа. 0: без изменений, -1: удалено, 1: добавлено.
        function computeDiff($from, $to)
        {
            $diffValues = array();
            $diffMask = array();

            $dm = array();
            $n1 = count($from);
            $n2 = count($to);

            for ($j = -1; $j < $n2; $j++) $dm[-1][$j] = 0;
            for ($i = -1; $i < $n1; $i++) $dm[$i][-1] = 0;
            for ($i = 0; $i < $n1; $i++)
            {
                for ($j = 0; $j < $n2; $j++)
                {
                    if ($from[$i] == $to[$j])
                    {
                        $ad = $dm[$i - 1][$j - 1];
                        $dm[$i][$j] = $ad + 1;
                    }
                    else
                    {
                        $a1 = $dm[$i - 1][$j];
                        $a2 = $dm[$i][$j - 1];
                        $dm[$i][$j] = max($a1, $a2);
                    }
                }
            }

            $i = $n1 - 1;
            $j = $n2 - 1;
            while (($i > -1) || ($j > -1))
            {
                if ($j > -1)
                {
                    if ($dm[$i][$j - 1] == $dm[$i][$j])
                    {
                        $diffValues[] = $to[$j];
                        $diffMask[] = 1;
                        $j--;
                        continue;
                    }
                }
                if ($i > -1)
                {
                    if ($dm[$i - 1][$j] == $dm[$i][$j])
                    {
                        $diffValues[] = $from[$i];
                        $diffMask[] = -1;
                        $i--;
                        continue;
                    }
                }
                {
                    $diffValues[] = $from[$i];
                    $diffMask[] = 0;
                    $i--;
                    $j--;
                }
            }

            $diffValues = array_reverse($diffValues);
            $diffMask = array_reverse($diffMask);

            return array('values' => $diffValues, 'mask' => $diffMask);
        }

        function renderComputeDiff($diff)
        {
            $diff_val = $diff['values'];
            $diff_mask = $diff['mask'];

            $n = count($diff_val);
            $pmc = 0;
            $result = '';
            for ($i = 0; $i < $n; $i++)
            {
                $mc = $diff_mask[$i];
                if ($mc != $pmc)
                {
                    switch ($pmc)
                    {
                        case -1: $result .= '</del>'; break;
                        case 1: $result .= '</ins>'; break;
                    }
                    switch ($mc)
                    {
                        case -1: $result .= '<del>'; break;
                        case 1: $result .= '<ins>'; break;
                    }
                }
                $result .= $diff_val[$i];

                $pmc = $mc;
            }
            switch ($pmc)
            {
                case -1: $result .= '</del>'; break;
                case 1: $result .= '</ins>'; break;
            }

            return $result;
        }

        $diff = computeDiff(mb_str_split($from_text), mb_str_split($to_text));
        $diff2 = computeDiff(mb_str_split(str_replace(' ', '',$from_text)), mb_str_split(str_replace(' ', '',$to_text)));

        $count_adding = 0;
        $count_adding_without_symbol = 0;

        foreach ($diff['mask'] as $_ => $value) {
            if($value == 1) {
                $count_adding++;
            }
        }
        foreach ($diff2['mask'] as $_ => $value) {
            if($value == 1) {
                $count_adding_without_symbol++;
            }
        }

        return [
            'count_symbol_with_space' => mb_strlen($to_text) - mb_strlen($from_text),
            'count_symbol_without_space' => mb_strlen(str_replace(' ', '', $to_text)) - mb_strlen(str_replace(' ', '', $from_text)),
            'count_new_symbol_with_space' => $count_adding,
            'count_new_symbol_without_space' => $count_adding_without_symbol,
            'html' => renderComputeDiff($diff)
        ];
    }
}
