<?php

namespace jooxMusic;

use GuzzleHttp\Client;

class Api
{

    protected $_client;

    static $baseUrl = '';

    private $_errMsg = '';

    public function __construct()
    {
        $this->_client = new Client();
    }


    /**
     * 获取歌手信息
     * @param string $singerId
     * @return array
     */
    public function getSingerInfo($singerId)
    {
        $url = "https://api-jooxtt.sanook.com/openjoox/v1/artist/{$singerId}?country=hk&lang=zh_TW";
        $data = $this->_sendRequest($url);
        if ($data === false) {
            return $this->_error();
        }
        return $this->_success($data);
    }


    /**
     * 获取歌手专辑
     * @param string $singerId
     * @param integer $page
     * @param integer $pageSize 最多50
     * @return array
     */
    public function getSingerAlbums($singerId, $page = 1, $pageSize = 50)
    {
        $page > 0 || $page = 1;
        $pageSize > 0 || $pageSize = 50;
        $url = "https://api-jooxtt.sanook.com/openjoox/v1/artist/{$singerId}/albums";
        $param = [
            'country' => 'hk',
            'lang' => 'zh_TW',
            'index' => ($page - 1) * $pageSize,
            'num' => $pageSize
        ];
        $result = $this->_sendRequest($url, $param);
        if ($result === false) {
            return $this->_error();
        }
        if (!isset($result['albums']['items'])) {
            return $this->_error('singer albums not found.');
        }
        $data = $result['albums']['items'];
        return $this->_success($data);
    }


    /**
     * 获取专辑歌曲
     * @param string $albumId
     * @return array
     */
    public function getAlbumSongs($albumId, $index = 0, &$data = [])
    {
        $url = "https://api-jooxtt.sanook.com/openjoox/v1/album/$albumId/tracks";
        $param = [
            'country' => 'hk',
            'lang' => 'zh_TW',
            'index' => $index,
            'num' => 50
        ];
        $result = $this->_sendRequest($url, $param);
        if ($result === false) {
            return $this->_error();
        }
        if ($data) {
            $track = $result['tracks'];
            $data['songs'] = array_merge($data['songs'], $track['items']);
        } else {
            $data = $result;
            $track = $data['tracks'];
            $data['songs'] = $track['items'];
            $data['total_count'] = $track['total_count'];
            unset($data['vip_flag'], $data['error'], $data['tracks']);
        }
        if ($track['next_index'] > 0) {
            $this->getAlbumSongs($albumId, $track['next_index'], $data);
        }
        return $this->_success($data);
    }


    /**
     * 获取歌手歌曲
     * @param string $singerId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getSingerSongs($singerId, $page = 1, $pageSize = 50)
    {
        $page > 0 || $page = 1;
        $pageSize > 0 || $pageSize = 50;
        $url = "https://api-jooxtt.sanook.com/openjoox/v1/artist/{$singerId}/tracks";
        $param = [
            'country' => 'hk',
            'lang' => 'zh_TW',
            'index' => ($page - 1) * $pageSize,
            'num' => $pageSize
        ];
        $result = $this->_sendRequest($url, $param);
        if ($result === false) {
            return $this->_error();
        }
        if (!isset($result['tracks']['items'])) {
            return $this->_error('singer tracks not found.');
        }
        $data = $result['tracks']['items'];
        return $this->_success($data);
    }


    /**
     * 获取歌曲信息
     * @param string $songId
     * @return array
     */
    public function getSongInfo($songId)
    {
        $url = "https://api-jooxtt.sanook.com/page/single";
        $param = [
            'regionURI' => 'hk-zh_hk',
            'country' => 'hk',
            'lang' => 'zh_hk',
            'id' => $songId,
            // 'device' => 'desktop'
        ];
        $result = $this->_sendRequest($url, $param);
        if ($result === false) {
            return $this->_error();
        }
        $data = $result['single'] ?: [];
        if (!empty($result['comments']['total_comments'])) {
            $data['total_comments'] = $result['comments']['total_comments'];
        } else {
            $data['total_comments'] = 0;
        }
        $data['allowed_regions'] = $result['hreflangs']['allowed_regions'];
        return $this->_success($data);
    }


    /**
     * 获取榜单歌曲
     * @param string $rankId
     * @return array
     */
    public function getChartSongs($rankId, $index = 0, &$data = [])
    {
        $url = "https://api-jooxtt.sanook.com/openjoox/v1/toplist/{$rankId}/tracks";
        $param = [
            'country' => 'hk',
            'lang' => 'zh_TW',
            'index' => $index,
            'num' => 50
        ];
        $result = $this->_sendRequest($url, $param);
        if ($result === false) {
            return $this->_error();
        }
        $songs = $result['tracks']['items'] ?: [];
        $data = array_merge($data, $songs);
        if ($result['tracks']['next_index'] > 0) {
            $this->getChartSongs($rankId, $result['tracks']['next_index'], $data);
        }
        return $this->_success($data);
    }


    /**
     * 获取歌手排行榜数据
     * @param string $singerId
     * @return array
     */
    public function getSingersRankInfo($singerId)
    {
        $url = "https://api-jooxtt.sanook.com/openjoox/v1/toplists?country=hk&lang=zh_TW";
        $result = $this->_sendRequest($url);
        if ($result === false) {
            return $this->_error();
        }
        
        $chartlist = $result['toplists'] ?: [];
        $return = [];
        foreach ($chartlist as $rank) {
            $data = $this->getChartSongs($rank['id']);
            if ($data['ret']) {
                $list = $data['data'];
                $songs = [];
                foreach ($list as $key => $song) {
                    foreach ($song['artist_list'] as $artist) {
                        if ($artist['id'] == $singerId) {
                            $singername = array_column($song['artist_list'], 'name');
                            $song['singer_name'] = implode(',', $singername);
                            $song['rank'] = $key + 1;
                            unset($song['artist_list'], $song['has_hifi'], $song['has_hq'], $song['qrc_exist'], $song['track_label_flag'], $song['lrc_exist'], $song['vip_flag'], $song['is_playable']);
                            $songs[] = $song;
                        }
                    }
                }
                if ($songs) {
                    $return[] = [
                        'top_id' => $rank['id'],
                        'top_name' => $rank['name'],
                        'update_time' => $rank['update_time'],
                        'songs' => $songs
                    ];
                }
            }
            usleep(500);
        }
        return $this->_success($return);
    }

    private function _sendRequest($url, $param = [])
    {
        if ($param) {
            $url .= '?' . http_build_query($param);
        }
        try {
            $response = $this->_client->get($url);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->_error(__METHOD__. ', client error [' . $e->getCode() . '] ' . $e->getMessage(), false);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            return $this->_error(__METHOD__. ', server error [' . $e->getCode() . '] ' . $e->getMessage(), false);
        } catch (\Exception $e) {
            return $this->_error(__METHOD__. ', server error [' . $e->getCode() . '] ' . $e->getMessage(), false);
        }
        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }


    private function _success($data = [])
    {
        return ['ret' => true, 'data' => $data, 'msg' => ''];
    }

    private function _error($msg = '', $isArray = true)
    {
        if ($isArray) {
            return ['ret' => false, 'data' => null, 'msg' => $msg ?: $this->_errMsg];
        }

        $this->_errMsg = $msg;
        return false;
    }
}
