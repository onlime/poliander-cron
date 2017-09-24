<?php

require_once __DIR__ . '/../src/Cron.php';

/**
 * Tests for \Cron class
 */
class CronTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider parserTestProvider
     */
    public function testParser($expression, $valid = false, $now = 'now', $matching = false)
    {
        $ct = new Cron($expression, new DateTimeZone('Europe/Berlin'));
        $dt = new DateTime($now, new DateTimeZone('Europe/Berlin'));

        $this->assertEquals($valid, $ct->isValid());
        $this->assertEquals($matching, $ct->isMatching($dt->getTimeStamp()));
    }

    /**
     * @return array
     */
    public function parserTestProvider()
    {
        return [
            ["* * * * *",                true, 'now', true],
            [" *\t    *\t* *  * \t  ",   true, 'now', true],
            ["1 0 * * *",                true, '2017-05-26 00:01', true],
            ["0 * * * *",                true, '2013-02-13 12:00', true],
            ["* * 1 * *",                true, '2013-03-01 21:43', true],
            ["* * * * 7",                true, '2013-04-07 06:54', true],
            ["* 23 * * *",               true, '2012-05-23 23:23', true],
            ["* 23 * * *",               true, '2012-05-23 21:23', false],
            ["* * * * sat",              true, '1994-10-01 23:45', true],
            ["* * * * sat",              true, '1993-10-01 23:45', false],
            ["* * * aug *",              true, '2001-08-09 00:15', true],
            ["*/5 * * * *",              true, '2013-09-10 04:45', true],
            ["*/5 * * * *",              true, '2013-09-10 04:43', false],
            ["0-30 * * * *",             true, '2010-08-01 08:15', true],
            ["0-30 * * * *",             true, '2010-08-01 08:45', false],
            ["* 22-23 * * *",            true, '2006-05-07 22:30', true],
            ["10-50/10 * * * *",         true, '2005-11-12 14:40', true],
            ["10-30/5,45 * * * *",       true, '2009-08-10 15:15', true],
            ["10-30/5,45 * * * *",       true, '2009-09-11 15:45', true],
            ["5-15,45-55 * * * *",       true, '2010-08-02 10:05', true],
            ["5-10,15,20-25 * * * *",    true, '2011-07-16 07:04', false],
            ["5-10,15,20-25 * * * *",    true, '2011-07-16 07:07', true],
            ["5-10,15,20-25 * * * *",    true, '2011-07-16 07:15', true],
            ["5-10,15,20-25 * * * *",    true, '2011-07-16 07:17', false],
            ["50-10/5,20,30-40 * * * *", true, '2006-12-31 23:55', true],
            ["50-10/5,20,30-40 * * * *", true, '2006-12-31 23:56', false],
            ["50-10/5,20,30-40 * * * *", true, '2006-12-31 23:35', true],
            ["50-10/5,20,30-40 * * * *", true, '2007-01-01 00:45', false],
            ["50-10/5,20,30-40 * * * *", true, '2007-01-01 00:05', true],
            ["58-2/2 * * * *",           true, '2015-07-16 08:56', false],
            ["58-2/2 * * * *",           true, '2015-07-16 08:57', false],
            ["58-2/2 * * * *",           true, '2015-07-16 08:58', true],
            ["58-2/2 * * * *",           true, '2015-07-16 08:59', false],
            ["58-2/2 * * * *",           true, '2015-07-16 09:00', true],
            ["58-2/2 * * * *",           true, '2015-07-16 09:01', false],
            ["58-2/2 * * * *",           true, '2015-07-16 09:02', true],
            ["58-2/2 * * * *",           true, '2015-07-16 09:03', false],
            ["58-2/2 * * * *",           true, '2015-07-16 09:04', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 14:01', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 15:02', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 16:03', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 17:01', true],
            ["* 17-1/3 * * *",           true, '2014-01-30 18:02', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 19:03', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 20:01', true],
            ["* 17-1/3 * * *",           true, '2014-01-30 21:02', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 22:03', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 23:01', true],
            ["* 17-1/3 * * *",           true, '2014-01-30 00:01', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 01:02', false],
            ["* 17-1/3 * * *",           true, '2014-01-30 02:03', false],
            ["* * * * 7",                true, '2013-07-07 00:00', true],
            ["* * * * 0",                true, '2013-07-07 00:00', true],
            ["1-1 * * * *",              true, '2012-04-07 00:01', true],

            ["foobar"],
            ["* * * *"],
            ["a * * * *"],
            ["* * 0 * *"],
            ["* * * * 8"],
            ["*/ * * * "],
            ["*/foo * * * *"],
            ["* * */32 * *"],
            ["* * * 32 *"],
            ["// * * * *"],
            ["*5 * * * *"],
            ["5- * * * *"],
            ["/5 * * * *"],
            ["-6 * * * *"],
            ["60 * * * *"],
            ["* 24 * * *"],
            ["* * * * * *"],
            ["4/2 * * * *"],
            ["* * * * foo"],
            ["* * * bar *"],
            ["5-60 * * * *"],
            ["* 23-24 * * *"],
            ["*/60 0-12 * * *"],
            ["5-20/5 22-23- * * *"],
            ["50-10/5,,30-40 * * * *"],

            ["* * * * mon,tue"], // see crontab(5) man page
            ["* * * jan,feb *"], // see crontab(5) man page
        ];
    }

    /**
     * @dataProvider getNextProvider
     */
    public function testGetNext($expression, $timestamp, $nextmatch)
    {
        $ct = new Cron($expression, new \DateTimeZone('Europe/Berlin'));
        $this->assertEquals($ct->getNext($timestamp), $nextmatch);
    }

    /**
     * @return array
     */
    public function getNextProvider()
    {
        return [
            ['* * 13 * fri', 1400407467, 1402610400],
            ['*/15 * * * *', 1400407520, 1400408100],
            ['1 0 * * *', 1495697149, 1495749660],
            ['1 0 * * *', 1496223073, 1496268060]
        ];
    }

    public function testIsMatchingWithDateTime()
    {
        $cron = new Cron('45 9 * * *', new DateTimeZone('Europe/Berlin'));
        $dt = new DateTime('2014-05-18 08:45', new DateTimeZone('Europe/London'));
        $this->assertEquals(true, $cron->isMatching($dt));
    }

    public function testGetNextWithDateTime()
    {
        $cron = new Cron('45 9 * * *', new DateTimeZone('Europe/Berlin'));
        $dt = new DateTime('2014-05-18 08:40', new DateTimeZone('Europe/London'));

        $this->assertEquals(1400399100, $cron->getNext($dt));
    }

    public function testGetNextWithoutParameter()
    {
        $cron = new Cron('* * * * *');
        $this->assertEquals(ceil((60 + time()) / 60) * 60, $cron->getNext());
    }

    public function testGetNextWithTimestamp()
    {
        $tz = new DateTimezone('Europe/Berlin');
        $dt = new DateTime('2014-12-31 23:42', $tz);
        $cron = new Cron('45 9 29 feb thu', $tz);

        $this->assertEquals(1709196300, $cron->getNext($dt->getTimestamp()));
    }

    public function testGetNextRepeatedly()
    {
        $t = 1496478227;
        $cron = new Cron('*/30 */2 * * *');

        $this->assertEquals(1496478600, $t = $cron->getNext($t));
        $this->assertEquals(1496484000, $t = $cron->getNext($t));
        $this->assertEquals(1496485800, $t = $cron->getNext($t));
        $this->assertEquals(1496491200, $t = $cron->getNext($t));
    }
}
