<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command\Redis;

/**
 * @group commands
 * @group realm-string
 */
class INCRBY_Test extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\Redis\INCRBY';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'INCRBY';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 5);
        $expected = array('key', 5);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(5, $this->getCommand()->parseResponse(5));
    }

    /**
     * @group connected
     */
    public function testCreatesNewKeyOnNonExistingKey()
    {
        $redis = $this->getClient();

        $this->assertSame(10, $redis->incrby('foo', 10));
        $this->assertEquals(10, $redis->get('foo'));
    }

    /**
     * @group connected
     */
    public function testReturnsTheValueOfTheKeyAfterIncrement()
    {
        $redis = $this->getClient();

        $redis->set('foo', 2);

        $this->assertSame(22, $redis->incrby('foo', 20));
        $this->assertSame(10, $redis->incrby('foo', -12));
        $this->assertSame(-100, $redis->incrby('foo', -110));
    }

    /**
     * @group connected
     */
    public function testThrowsExceptionOnDecrementValueNotInteger()
    {
        $this->expectException('Predis\Response\ServerException');
        $this->expectExceptionMessage('ERR value is not an integer or out of range');

        $redis = $this->getClient();

        $redis->incrby('foo', 'bar');
    }

    /**
     * @group connected
     */
    public function testThrowsExceptionOnKeyValueNotInteger()
    {
        $this->expectException('Predis\Response\ServerException');
        $this->expectExceptionMessage('ERR value is not an integer or out of range');

        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->incrby('foo', 10);
    }

    /**
     * @group connected
     */
    public function testThrowsExceptionOnWrongType()
    {
        $this->expectException('Predis\Response\ServerException');
        $this->expectExceptionMessage('Operation against a key holding the wrong kind of value');

        $redis = $this->getClient();

        $redis->lpush('metavars', 'foo');
        $redis->incrby('metavars', 10);
    }
}