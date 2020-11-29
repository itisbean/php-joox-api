# php-joox-api

joox music's public api for php

## Composer install

```php
composer require itisbean/php-joox-api
```

## Usage

```php
// 引入autoload.php（框架中使用不需要）
include_once __DIR__.'/../vendor/autoload.php';
$api = new Api();
$ret = $api->getChartSongs(1);
```

## Function

### Get singer info by singerID

```php
/**
 * 获取歌手信息
 * @param string $singerId
 * @return array
 */
public function getSingerInfo($singerId);
```

### Get singer's albums

```php
/**
 * 获取歌手专辑
 * @param string $singerId
 * @param integer $page
 * @param integer $pageSize 最多50
 * @return array
 */
public function getSingerAlbums($singerId, $page = 1, $pageSize = 50);
```

### Get tracks of an album

```php
/**
 * 获取专辑歌曲
 * @param string $albumId
 * @return array
 */
public function getAlbumSongs($albumId, $index = 0, &$data = []);
```

### Get singer's songs

```php
/**
 * 获取歌手歌曲
 * @param string $singerId
 * @param integer $page
 * @param integer $pageSize
 * @return array
 */
public function getSingerSongs($singerId, $page = 1, $pageSize = 50);
```

### Get track info

```php
/**
 * 获取歌曲信息
 * @param string $songId
 * @return array
 */
public function getSongInfo($songId);
```

### Get songs of charts

```php
/**
 * 获取榜单歌曲
 * @param string $rankId
 * @return array
 */
public function getChartSongs($rankId, $index = 0, &$data = []);
```

### Get the singer's songs in charts

```php
/**
 * 获取歌手排行榜数据
 * @param string $singerId
 * @return array
 */
public function getSingersRankInfo($singerId);
```
