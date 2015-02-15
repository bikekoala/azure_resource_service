<?PHP
/**
 * curl库的封装
 *
 * @author Xuewu Sun <sunxw@ucloudworld.com> 2015-01-15
 */
class Curl
{
    const FT_JSON   = 'json';
    const FT_SERIAL = 'serialize';

    const MD_GET    = 'GET';
    const MD_PUT    = 'PUT';
    const MD_POST   = 'POST';
    const MD_DELETE = 'DELETE';

    /**
     * curl 句柄
     *
     * @var resource
     */
    protected $ch;

    /**
     * url地址
     *
     * @var string
     */
    protected $url;

    /**
     * 最后执行信息
     *
     * @var array
     */
    protected $lastInfo;

    /**
     * 构造函数
     *
     * @param string $url
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->ch = curl_init($url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true);
    }

    /**
     * 设置curl选项
     *
     * @param mixed $type 需要设置的CURLOPT_XXX选项
     * @param mixed $val
     * @return void
     */
    public function setopt($type, $val)
    {
        curl_setopt($this->ch, $type, $val);
    }

    /**
     * 发送post请求的快捷方法
     *
     * @param mixed $fields
     * @return string
     */
    public function post($fields = null)
    {
        $fields = is_array($fields) ? http_build_query($fields) : $fields;

        curl_setopt($this->ch, CURLOPT_POST, count($fields));
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($this->ch);
        $this->lastInfo = curl_getinfo($this->ch);
        return $result;
    }

    /**
     * 发送put请求的快捷方法
     *
     * @param mixed $fields
     * @return string
     */
    public function put($fields = null)
    {
        $fields = is_array($fields) ? http_build_query($fields) : $fields;

        //curl_setopt($this->ch, CURLOPT_PUT, count($fields));
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($this->ch);
        $this->lastInfo = curl_getinfo($this->ch);
        return $result;
    }

    /**
     * 发送get请求的快捷方法
     *
     * @param mixed $fields
     * @return string
     */
    public function get($fields = null)
    {
        if (is_array($fields)) {
            $info = curl_getinfo($this->ch);    
            $url = $info['url'] . '?' . http_build_query($fields);
            curl_setopt($this->ch, CURLOPT_URL, $url);
        }
        $result = curl_exec($this->ch);
        $this->lastInfo = curl_getinfo($this->ch);
        return $result;
    }

    /**
     * 接口请求处理,支持serialize&json
     *
     * @param mixed $fields
     * @param string $method GET|POST|PUT|DELETE
     * @param string $format
     * @return array
     */
    public function rest($fields = null, $method, $format = null)
    {
        switch ($method) {
            case self::MD_POST :
                $data = $this->post($fields);
                break;
            case self::MD_PUT :
                $data = $this->put($fields);
                break;
            case self::MD_GET :
            default :
                $data = $this->get($fields);
        }
        $data = preg_replace('/[^\x20-\xff]*/', '', $data); //清除不可见字符
        $data = iconv('utf-8', 'utf-8//ignore', $data); //UTF-8转码

        switch ($format) {
            case self::FT_SERIAL :
                if (false === ($result = unserialize($data))) {
                    throw new \Exception(
                        $data,
                        $this->lastInfo['http_code']
                    );
                }
                break;
            case self::FT_JSON :
            default :
                if (null === ($result = json_decode($data, true))) {
                    throw new \Exception(
                        $data,
                        $this->lastInfo['http_code']
                    );
                }
        }
        return $result;
    }

    /**
     * 最后一次请求的信息记录
     *
     * @return void
     */
    public function lastInfo()
    {
        return $this->lastInfo;
    }
}
