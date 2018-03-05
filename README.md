单例模式的三个要点：
（1）. 需要一个保存类的唯一实例的静态成员变量:
private static $_instance;   

（2）. 构造函数和克隆函数必须声明为私有的，防止外部程序new类从而失去单例模式的意义:
private function __construct()   
{   
    $this->_db = pg_connect('xxxx');  
}   
private function __clone()  
{  
}//覆盖__clone()方法，禁止克隆  

（3）. 必须提供一个访问这个实例的公共的静态方法（通常为getInstance方法），从而返回唯一实例的一个引用 
public static function getInstance()    
{    
    if(! (self::$_instance instanceof self) )   
    {    
        self::$_instance = new self();    
    }  
    return self::$_instance;    
  
}
