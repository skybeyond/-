存在兼容问题

oncopy 用来复制触发函数
onpaste 粘贴触发函数



跨站点XMLHttpRequest

现代浏览器通过实现Web应用程序（WebApps）工作组的跨站点请求访问控制标准来支持跨站点请求。
只要服务器配置为允许来自Web应用程序源的请求（Access-Control-Allow-Origin），XMLHttpRequest就可以工作。否则，INVALID_ACCESS_ERR会引发异常。

var formData = new FormData();
        formData.append("username", "Groucho");
        formData.append("accountnum", 123456); //数字123456会被立即转换成字符串 "123456"
        var request = new XMLHttpRequest();
        $(".k").on('click',function() {
            request.open("POST", "https://m.shop.hapi123.net/qqmusic/bm");
            request.send(formData);
        });



jqajax上传图片
<form id="ff" method="post" enctype="multipart/form-data">
    <input type="file" name="file" value="上传文件">
    <input type="text" name="username" value="">
    <input type="text" name="password" value="">
    <input type="submit" class="k" value="jj">
</form>
$(".k").on('click',function(){

            var form = new FormData($("#ff")[0]);
//            console.log(form);
//            var form = $("#ff").serializeArray();
            $.ajax({
                type:"POST",
                data:form,
                url:"/m.php",
                processData:false,
                contentType: false,
                success:function(e){

                }
            });
        })
