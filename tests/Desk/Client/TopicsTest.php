<?php

namespace Desk\Client;

use Desk\Client;
use Desk\Transport\Response;

require_once(dirname(__FILE__) . '/AbstractClientTestCase.php');

class TopicsTest extends AbstractClientTestCase
{

	protected function getType()
	{
		return Client::TOPICS;
	}

	public function testCreate()
	{
		$client = $this->client();

		$client->transport()
			->shouldReceive('post')
			->with(
				'/api/v1/topics.json',
				array(
					'name'           => 'abcd',
					'show_in_portal' => true,
					'description'    => 'efgh',
				)
			)
			->andReturn(new Response('{
				"success": true,
				"results": {
					"topic": {
						"id": 1234,
						"name": "abcd",
						"description": "efgh",
						"show_in_portal": true
					}
				}
			}'));

		$topicId = $client->create('abcd', 'efgh', true);

		$this->assertEquals(1234, $topicId, "Created topic ID \"$topicId\" differs from the expected ID 1234");
	}

	public function testCreateNoDescription()
	{
		$client = $this->client();

		$client->transport()
			->shouldReceive('post')
			->with(
				'/api/v1/topics.json',
				array(
					'name'           => 'bcde',
					'show_in_portal' => false,
				)
			)
			->andReturn(new Response('{
				"success": true,
				"results": {
					"topic": {
						"id": 2345,
						"name": "bcde",
						"description": "",
						"show_in_portal": false
					}
				}
			}'));

		$topicId = $client->create('bcde');

		$this->assertEquals(2345, $topicId, "Created topic ID \"$topicId\" differs from the expected ID 2345");
	}

	public function testCreateNoNameFail()
	{
		$client = $this->client();

		$client->transport()
			->shouldReceive('get', 'post', 'put', 'delete')->never();

		$this->setExpectedException('\Desk\Exception\InvalidArgumentException');
		$client->create('');
	}

	public function testRetrieve()
	{
		$client = $this->client();

		$client->transport()
			->shouldReceive('get')
			->with("/api/v1/topics/3456.json", array())
			->andReturn(new Response('{
				"topic": {
					"id": 3456,
					"name": "Topic Name",
					"description": "Description",
					"show_in_portal": true
				}
			}'));

		$topic = $client->retrieve(3456);

		$this->assertEquals(3456, $topic->id, "Retrieved topic ID \"{$topic->id}\" differs from the expected ID 3456");
		$this->assertEquals('Topic Name', $topic->name, "Retrieved topic name \"{$topic->name}\" differs from the expected name \"Topic Name\"");
		$this->assertEquals('Description', $topic->description, "Retrieved topic description \"{$topic->description}\" differs from the expected description \"Description\"");
		$this->assertTrue($topic->show_in_portal);
	}

	public function testRetrieveInvalidId()
	{
		$client = $this->client();

		$client->transport()
			->shouldReceive('get', 'post', 'put', 'delete')->never();

		$this->setExpectedException('\Desk\Exception\InvalidArgumentException');
		$client->retrieve('foobar');
	}

	public function testRetrieveAll()
	{
		 $client = $this->client();

		 $client->transport()
		 	->shouldReceive('get')
		 	->with('/api/v1/topics.json', array('count' => 1, 'page' => 2))
			->andReturn(new Response('{
				"results": [{
					"topic": {
						"id": 2,
						"name": "Canned Responses",
						"description": "Internal responses to common questions",
						"show_in_portal": false,
						"position": 2
					}
				}],
				"page": 2,
				"count": 1,
				"total": 3
			}'));

		$client->retrieveAll(1, 2);
	}

	public function testUpdate()
	{
		$client = $this->client();

		$fields = array(
			'name' => 'New Example Topic Name',
		);

		$client->transport()
			->shouldReceive('put')
			->with('/api/v1/topics/4567.json', $fields)
			->andReturn(new Response('{
				"success": true,
				"results": {
					"topic": {
						"id": 4567,
						"name": "New Example Topic Name",
						"description": "Old Description",
						"show_in_portal": true
					}
				}
			}'));

		$client->update(4567, $fields);
	}

	public function testUpdateTranslation()
	{
		$client = $this->client();

		$client->transport()
			->shouldReceive('put')
			->with(
				'/api/v1/topics/5678.json',
				array(
					'language' => 'de',
					'name'     => 'newName',
				)
			)
			->andReturn(new Response('{
				"success": true,
				"results": {
					"topic": {
						"id": 5678,
						"name": "newName",
						"description": "Description",
						"show_in_portal": true
					}
				}
			}'));

		$client->updateTranslation(5678, 'de', 'newName');
	}

	public function testDestroy()
	{
		$client = $this->client();

		$client->transport()
			->shouldReceive('delete')
			->with('/api/v1/topics/6789.json', array())
			->andReturn(new Response('{
				"success": true
			}'));

		$client->destroy(6789);
	}

	public function testDestroyInvalidId()
	{
		$client = $this->client();

		$client->transport()
			->shouldReceive('get', 'post', 'put', 'delete')->never();

		$this->setExpectedException('\Desk\Exception\InvalidArgumentException');
		$client->destroy('barbaz');
	}

}
