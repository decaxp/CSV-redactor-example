<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-control" content="NO-CACHE">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSV Dolgov</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/jquery-ui.min.css" rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://yastatic.net/jquery/3.1.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.file-input.js"></script>
    <script src="js/cookie.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/draggable.js"></script>

    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="header">
        <h3 class="text-muted">CSV Dolgov</h3>
    </div>

    <div class="jumbotron">
        <p class="lead">Онлайн - редактор CSV файлов</p>
                <input type="hidden" name="uploadFileName" id="uploadFileName">

                <form id="fileUploadForm" name="fileUploadForm" action="save.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="delimeter">Разделитель</label>
                            <input type="text" class="form-control" id="delimeter" name="delimeter" value=",">
                        </div>
                    </div>
                    <div class="row marginTop20">
                        <div class="col-md-4">
                            <label for="fromEncoding">Кодировка</label>
                            <select class="form-control" name="fromEncoding" id="fromEncoding">
                                <option selected value="utf-8">utf-8</option>
                                <option value="windows-1251">windows-1251</option>
                            </select>
                        </div>
                    </div>
                    <div class="row marginTop20">
                        <div class="col-md-4">
                            <input id="userfile" type="file" name="userfile" title="Обзор файлов" class="btn-primary ">
                            <input type="submit" name="submit"  class="btn btn-info fright" value="Загрузить">
                            <div class="clear"></div>
                        </div>
                    </div>
                </form>
    </div>

    <hr>

    <table id="csvTable" cellspacing="0" cellpadding="2" class="table table-bordered table-hover"></table>

    <input type="button" onClick="sendForm()" class="none lastBlock btn btn-primary" value="Выгрузить">
    <input type="button" onClick="deleteForm()" class="none lastBlock btn btn-danger" value="Стереть">

</div>
<script>
    $('input[type=file]').bootstrapFileInput();
    $('.file-inputs').bootstrapFileInput();
</script>
<script>
    var isUpload=false;
    var filename;
    var locationHref="http://csv:81/download.php";
    var delimeter=',';
    var fromEncoding='utf-8';

    $("#fileUploadForm").submit(function(){
        var formData = new FormData($(this)[0]);
        var url = "save.php"; // the script where you handle the form input.
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            beforeSend:function () {
//                var name=$('#userfile').val();
//                name=name.substring(name.lastIndexOf('\\')+1,name.length);
//                $('#uploadFileName').val(name);
                isUpload=false;
//                $('#csvTable tr').remove();

            },
            success: function(responseData, textStatus, jqXHR) {
                delimeter=$('#delimeter').val();
                fromEncoding=$('#fromEncoding').val();

                isUpload=true;

                var name=$('#userfile').val();
                name=name.substring(name.lastIndexOf('\\')+1,name.length);
                var date = new Date;
                date.setDate(date.getDate() + 30);//1 месяц
                setCookie('csvFileName',name);
                setCookie('csvDelimeter',delimeter);
                setCookie('csvEncoding',fromEncoding);
                filename=name;
                alert(responseData);

                ajaxFileReceive(name);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    });

    function makeTable(obj){
        var rows=Object.keys(obj).length;
        var cols=0;
        for(var key in obj){
            var len=Object.keys(obj[key]).length-1;

            if (cols<len){
                cols=len;
            }
        }
        var str="",value="";

        var counter=0;
        for(var i=0;i<rows;i++){
            str+="<tr>";
            for(var z=0;z<cols;z++){
                value=obj[i].hasOwnProperty(z)?obj[i][z]:"";
                str+="<td class='index'><input id='inp"+counter.toString()+"' draggable='true' ondragstart='drag(event)' ondrop='drop(event)' ondragover='allowDrop(event)' type='text' class='form-control input-md' value='"+value.toString()+"'></td>";
                counter++;
            }
            str+="</tr>";
        }
        $('#csvTable').html(str);

    }

    function ajaxFileReceive(name){
        var data={'type':'get','name':name,'delimeter':delimeter,'defaultCharset':'utf-8','fromCharset':fromEncoding};
        $.ajax({
            url: "edit.php",
            type: 'POST',
            data: data,
//            beforeSend:function () {
//                $('#csvTable tr').remove();
//            },
            success: function(responseData, textStatus, jqXHR) {
                console.log(responseData);
                var obj=JSON.parse(responseData);
                makeTable(obj);
                $('.none.btn').removeClass('none');

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }

        });
    }

    function tableTOJson() {
        var arr=[],row=[];
        $('#csvTable tr').each(function(i, tr){
            tds = $(tr).find('td>input');
            row=[];
            tds.each(function(index, inp){
                someText = inp.value.replace(/(\r\n|\n|\r)/gm,"");
                row.push(someText);
            });
            arr.push(row);
        });
        return JSON.stringify(arr);

    }

    function sendForm() {
        var data={'type':'save','name':filename,'delimeter':delimeter,'toEncoding':fromEncoding,'table':tableTOJson()};

        $.ajax({
            url: "edit.php",
            type: 'POST',
            data: data,
            success: function(responseData, textStatus, jqXHR) {
                    console.log(responseData);
                location.href=locationHref+"?name="+filename+'&charset='+fromEncoding;

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }

        });
    }

    function deleteForm() {
        var data={'type':'delete','name':filename};

        $.ajax({
            url: "edit.php",
            type: 'POST',
            data: data,
            success: function(responseData, textStatus, jqXHR) {
                console.log(responseData);
                if (responseData==2){//2 означает что удалено оба файла
                    alert("Файлы успешно удалены");

                }else{
                    alert(responseData);
                }
                $('.lastBlock').addClass('none');
                $('#csvTable tr').remove();
                deleteCookie('csvFileName');
                deleteCookie('csvEncoding');
                deleteCookie('csvDelimeter');

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }

        });
    }

    $(document).ready(function(){
        filename=getCookie('csvFileName');
        fromEncoding=getCookie('csvEncoding');
        delimeter=getCookie('csvDelimeter');
        if (filename!=undefined){
            ajaxFileReceive(filename);
        }

    });

</script>
</body>
</html>