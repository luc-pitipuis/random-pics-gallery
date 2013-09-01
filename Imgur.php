<?php
/**
 * PHP Imgur wrapper 0.1
 * Imgur API wrapper for easy use.
 * @author Vadim Kr.
 * @copyright (c) 2013 bndr
 * @license http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */

/**
 * Fork in one one file, stripped down
 */

class Connect
{

    /**
     * @var array
     */
    protected $options;
    /**
     * @var string
     */
    protected $api_key;
    /**
     * @var string
     */
    protected $api_secret;
    /**
     * @var string
     */
    protected $api_endpoint;
    /**
     * @var string
     */
    protected $access_token;
    /**
     * @var string
     */
    protected $refresh_token;
    /**
     * @var string
     */
    protected $oauth = "https://api.imgur.com/oauth2";

    /**
     * Constructor
     * @param string $api_key
     * @param string $api_secret
     */
    function __construct($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    /**
     * Set Access Data. Used for authorization
     * @param $accessToken
     * @param $refreshToken
     */
    function setAccessData($accessToken, $refreshToken)
    {
        $this->access_token = $accessToken;
        $this->refresh_token = $refreshToken;
    }

    /**
     * Make request to Imgur API endpoint
     * @param $endpoint
     * @param mixed $options
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    function request($endpoint, $options = FALSE, $type = "GET")
    {
        $headers = (!$this->access_token) ? array('Authorization: CLIENT-ID ' . $this->api_key) : array("Authorization: Bearer " . $this->access_token);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($options) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
        }
        if (($data = curl_exec($ch)) === FALSE) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);
        return json_decode($data, true);
    }
}

class Gallery
{

    /**
     * @var
     */
    protected $conn;

    /**
     * Cosntructor
     * @param $connection
     * @param string $endpoint
     */
    function __construct($connection, $endpoint)
    {
        $this->conn = $connection;
        $this->endpoint = $endpoint;
    }

    /**
     * Get data from gallery
     *
     * @param string $section  hot | top | user - defaults to hot
     * @param string $sort     viral | time - defaults to viral
     * @param int $page        integer - the data paging number
     * @return mixed
     */
    function get($section, $sort, $page)
    {
        $uri = $this->endpoint . "/gallery/" . $section . "/" . $sort . "/" . $page;
        return $this->conn->request($uri);
    }

    /**
     * Get subreddit gallery
     * @param $subreddit
     * @param $sort
     * @param $page
     * @param bool $window
     * @return mixed
     */
    function subreddit_gallery($subreddit, $sort, $page, $window = false)
    {
        $uri = $this->endpoint . "/gallery/r/" . $subreddit . "/" . $sort . ($window !== false ? "/" . $window : "") . "/" . $page;
        return $this->conn->request($uri);
    }

    /**
     * Get subreddit image
     * @param $subreddit
     * @param $id
     * @return mixed
     */
    function subreddit_image($subreddit, $id)
    {
        $uri = $this->endpoint . "/gallery/r/" . $subreddit . "/" . $id;
        return $this->conn->request($uri);
    }

    /**
     * Search for a string in gallery
     * @param string $str
     * @return mixed
     */
    function search($str)
    {
        $uri = $this->endpoint . "/gallery/search?q=" . $str;
        return $this->conn->request($uri);
    }

    /**
     * Get random images from gallery. Pagination is available
     * @param int $page
     * @return mixed
     */
    function random($page = 0)
    {
        $uri = $this->endpoint . "/gallery/random/random/" . $page;
        return $this->conn->request($uri);
    }

    /**
     * Submit Image | Album to gallery
     * @param string $id
     * @param $options
     * @param string $type
     * @return mixed
     */
    function submit($id, $options, $type = "image")
    {
        $uri = $this->endpoint . "/gallery/" . $type . "/" . $id;
        return $this->conn->request($uri, $options, "POST");
    }

    /**
     * Remove image from gallery
     * @param string $id
     * @return mixed
     */
    function remove($id)
    {
        $uri = $this->endpoint . "/gallery/" . $id;
        return $this->conn->request($uri, array("remove" => true), "DELETE");
    }

    /**
     * Get album information in gallery
     * @param string $id
     * @return mixed
     */
    function album_info($id)
    {
        $uri = $this->endpoint . "/gallery/album/" . $id;
        return $this->conn->request($uri);
    }

    /**
     * Get Image information in gallery
     * @param string $id
     * @return mixed
     */
    function image_info($id)
    {
        $uri = $this->endpoint . "/gallery/image/" . $id;
        return $this->conn->request($uri);
    }

    /**
     * Report Image | Album
     * @param string $id
     * @param string $type
     * @return mixed
     */
    function report($id, $type = "image")
    {
        $uri = $this->endpoint . "/gallery/" . $type . "/" . $id . "/report";
        return $this->conn->request($uri, array("report" => true), "POST");

    }

    /**
     *  Get votes for Image | Album
     * @param string $id
     * @param string $type
     * @return mixed
     */
    function votes($id, $type = "image")
    {
        $uri = $this->endpoint . "/gallery/" . $type . "/" . $id . "/votes";
        return $this->conn->request($uri);
    }


    /**
     * Vote on Image | ALbum. Votes can be either up or down.
     * @param string $id
     * @param string $type
     * @param string $vote
     * @return mixed
     */
    function vote($id, $type = "image", $vote = "up")
    {
        $uri = $this->endpoint . "/gallery/" . $type . "/" . $id . "/vote/" . $vote;
        return $this->conn->request($uri, array("vote" => true), "POST");
    }

    /**
     * Get comments for Image | Album
     * @param string $id
     * @param string $type
     * @return mixed
     */
    function comments($id, $type)
    {
        $uri = $this->endpoint . "/gallery/" . $type . "/" . $id . "/comments";
        return $this->conn->request($uri);
    }

    /**
     * Get a comment to an image in gallery
     * @param $image_id
     * @param $type
     * @param $comment_id
     * @return mixed
     */
    function comment($image_id, $type, $comment_id)
    {
        $uri = $this->endpoint . "/gallery/" . $type . "/" . $image_id . "/comment/" . $comment_id;
        return $this->conn->request($uri);
    }
}

class Imgur
{

    /**
     * @var bool|string
     */
    protected $api_key = "";
    /**
     * @var string
     */
    protected $api_secret = "";
    /**
     * @var string
     */
    protected $api_endpoint = "https://api.imgur.com/3";
    /**
     * @var string
     */
    protected $oauth_endpoint = "https://api.imgur.com/oauth2";
    /**
     * @var Connect
     */
    protected $conn;

    /**
     * Imgur Class constructor.
     * @param string $api_key
     * @param string $api_secret
     * @throws
     */
    function __construct($api_key, $api_secret)
    {
        if (!$api_key || !$api_secret) throw new Exception("Please provided API key data");

        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->conn = new Connect($this->api_key, $this->api_secret);
    }

    /**
     * oAuth2 authorization. If the acess_token needs to be refreshed pass $refresh_token as first parameter,
     * if this is the first time getting access_token from user, then set the first parameter to false, pass the auth code
     * in the second.
     * @param bool $refresh_token
     * @param bool $auth_code
     * @return array $tokens
     */
    function authorize($refresh_token = FALSE, $auth_code = FALSE)
    {
        $auth = new Authorize($this->conn, $this->api_key, $this->api_secret);
        $tokens = ($refresh_token) ? $auth->refreshAccessToken($refresh_token) : $auth->getAccessToken($auth_code);
        (!$tokens) ? $auth->getAuthorizationCode() : $this->conn->setAccessData($tokens['access_token'], $tokens['refresh_token']);

        return $tokens;

    }

    /**
     * Upload an image from url, bas64 string or file.
     * @return mixed
     */
    function upload()
    {
        $upload = new Upload($this->conn, $this->api_endpoint);
        return $upload;
    }

    /**
     * Image Wrapper for all image functions
     * @param string $id
     * @return Image
     */
    function image($id = null)
    {
        $image = new Image($id, $this->conn, $this->api_endpoint);
        return $image;
    }

    /**
     * Album wrapper for all album functions.
     * @param string $id
     * @return Album
     */
    function album($id = null)
    {
        $album = new Album($id, $this->conn, $this->api_endpoint);
        return $album;
    }

    /**
     * Account wrapper for all account functions
     * @param string $username
     * @return Account
     */
    function account($username)
    {
        $acc = new Account($username, $this->conn, $this->api_endpoint);
        return $acc;
    }

    /**
     * Gallery wrapper for all functions regarding gallery
     * @return Gallery
     */
    function gallery()
    {
        $gallery = new Gallery($this->conn, $this->api_endpoint);
        return $gallery;
    }

    /**
     * Comment wrapper for all commenting functions
     * @param string $id
     * @return Comment
     */
    function comment($id)
    {
        $comment = new Comment($id, $this->conn, $this->api_endpoint);
        return $comment;
    }

    /**
     * Messages wrapper
     * @return Message
     */
    function message()
    {
        $msg = new Message($this->conn, $this->api_endpoint);
        return $msg;
    }

    /**
     * Notifications wrapper
     * @return mixed
     */
    function notification()
    {
        $notification = new Notification($this->conn, $this->api_endpoint);
        return $notification;
    }

}

class Image
{

    /**
     * @var
     */
    protected $conn;
    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $id
     * @param $connection
     * @param string $endpoint
     */
    function __construct($id, $connection, $endpoint)
    {
        $this->conn = $connection;
        $this->endpoint = $endpoint;
        $this->id = $id;
    }

    /**
     * Get message by id
     * @return mixed
     */
    function get()
    {

        $uri = $this->endpoint . "/image/" . $this->id;
        return $this->conn->request($uri);

    }

    function get_multi()
    {
        $uri = array();
        foreach($this->id as $id) $uri[] = $this->endpoint . "/image/" . $id;
        return $this->conn->request_multi($uri);

    }

    /**
     * Update message by id
     * @param $options
     * @return mixed
     */
    function update($options)
    {
        $uri = $this->endpoint . "/image/" . $this->id;
        return $this->conn->request($uri, $options, "PUT");
    }

    /**
     * Delete message by id
     * @return mixed
     */
    function delete()
    {
        $uri = $this->endpoint . "/image/" . $this->id;
        return $this->conn->request($uri, array("delete" => true), "DELETE");
    }

    /**
     * Favorite message by id
     * @return mixed
     */
    function favorite()
    {
        $uri = $this->endpoint . "/image/" . $this->id . "/favorite";
        return $this->conn->request($uri, array('favorite' => true), "POST");
    }
}

?>
