<?php
/**
 *
 */
class RandBonus
{
    private $amount;
    private $total;
    private $max;
    private $min;
    private $average;

    public function __construct($_amount, $_total, $_max = null, $_min = null)
    {
        $this->amount  = $_amount;
        $this->total   = $_total;
        $this->max     = $_max;
        $this->min     = $_min;
        $this->average = $this->amount / $this->total;

        if ($this->max === null || $this->min === null) {
            $this->min = 1;
            $this->max = intval($this->average * 3);
        }

        if ($this->max < $this->average) {
            $this->max = intval($this->average * 3);
        }
    }

    public function getBonus()
    {
        if ($this->amount < $this->total) {
            return false;
        }

        for ($i = 0; $i < $this->total; $i++) {
            // 因为小红包的数量通常是要比大红包的数量要多的，因为这里的概率要调换过来。
            // 当随机数>平均值，则产生小红包
            // 当随机数<平均值，则产生大红包
            if (rand($this->min, $this->max) > $this->average) {
                // 在平均线上减钱
                $temp = $this->min + $this->xRandom($this->min, $this->average);
                $result[$i] = $temp;
                $this->amount -= $temp;
            } else {
                // 在平均线上加钱
                $temp = $this->max - $this->xRandom($this->average, $this->max);
                $result[$i] = $temp;
                $this->amount -= $temp;
            }
        }

        // 如果还有余钱，则尝试加到小红包里，如果加不进去，则尝试下一个。
        while ($this->amount > 0) {
            for ($i = 0; $i < $this->total; $i++) {
                if ($this->amount > 0 && $result[$i] < $this->max) {
                    $result[$i]++;
                    $this->amount--;
                }
            }
        }

        // 如果钱是负数了，还得从已生成的小红包中抽取回来
        while ($this->amount < 0) {
            for ($i = 0; $i < $this->total; $i++) {
                if ($this->amount < 0 && $result[$i] > $this->min) {
                    $result[$i]--;
                    $this->amount++;
                }
            }
        }

        return $result;
    }

    /**
     * 生产min和max之间的随机数，但是概率不是平均的，从min到max方向概率逐渐加大。
     * 先平方，然后产生一个平方值范围内的随机数，再开方，这样就产生了一种“膨胀”再“收缩”的效果。
     * @access public
     * @param  int    $_max
     * @param  int    $_min
     * @return int
     */
    private function xRandom($_max, $_min)
    {
        $sqr = intval($this->sqr($_max - $_min));
        $rand_num = rand(0, $sqr - 1);
        return intval(sqrt($rand_num));
    }

    /**
     * 求一个数的平方
     * @access private
     * @param  int     $n
     * @return int
     */
    private function sqr($_n)
    {
        return $_n * $_n;
    }
}
