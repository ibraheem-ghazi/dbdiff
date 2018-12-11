<?php
/**
 * Created by PhpStorm.
 * User: Ibraheem Ghazi Alnabriss
 * Github: https://github.com/ibraheem-ghazi
 * Date: 11/19/2018
 */
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Database Compare (Diff) with Details & Queries</title>
        <meta charset='utf8' />
        <style>
            body{padding-bottom:50px;}
            code {white-space: pre-wrap;background: #0c3435;color: #fff; margin: 15px;display: block;padding: 15px 15px 10px 15px;}
            h2{text-decoration:underline}
            div{padding: 10px 15px;margin: 15px 0px;}
            .new,ins {background: #e8ffcd;border:2px solid #9bbb75;white-space: pre-wrap;}
            .deleted,del {background: #ffe8cd;border: 2px solid #da9d56;}
            .normal {background: #f7f7f7;border: 2px solid grey;}
            del,ins,span{display:block;}
            code span{display:inline;}
            ins,del{    text-decoration: none;}
            span,ins,del{border:none;}
            body.only-codes > *:not(#codes):not(button) {display: none;}
            body.only-codes{margin: 0px;padding: 0px;background: #2d2b57 !important;}
            body.only-codes code {margin: 0px;padding: 5px;border-bottom: 1px solid #464377;}
            body:not(.only-codes) > #codes {display: none;}
            button{border: 1px solid deepskyblue;background: skyblue;color: #fff;font-size: 1.1em;width: 70px;height: 70px;
                border-radius: 100%;box-shadow: 1px 2px 1px 1px #ccc;position: fixed;z-index: 5;bottom: 25px;left: 15px;
                cursor:pointer;}
        
            /*highlightjs theme*/
            .hljs{display:block;overflow-x:auto;line-height:1.45;padding:2rem;background:#2d2b57 !important;font-weight:normal}.hljs-title{color:#fad000 !important;font-weight:normal}.hljs-name{color:#a1feff !important;}.hljs-tag{color:#ffffff !important;}.hljs-attr{color:#f8d000 !important;font-style:italic}.hljs-built_in,.hljs-selector-tag,.hljs-section{color:#fb9e00 !important;}.hljs-keyword{color:#fb9e00 !important;}.hljs,.hljs-subst{color:#e3dfff !important;}.hljs-string,.hljs-attribute,.hljs-symbol,.hljs-bullet,.hljs-addition,.hljs-code,.hljs-regexp,.hljs-selector-class,.hljs-selector-attr,.hljs-selector-pseudo,.hljs-template-tag,.hljs-quote,.hljs-deletion{color:#4cd213 !important;}.hljs-meta,.hljs-meta-string{color:#fb9e00 !important;}.hljs-comment{color:#ac65ff}.hljs-keyword,.hljs-selector-tag,.hljs-literal,.hljs-name,.hljs-strong{font-weight:normal}.hljs-literal,.hljs-number{color:#fa658d !important;}.hljs-emphasis{font-style:italic}.hljs-strong{font-weight:bold}
       
       
            div.constraints{padding:0px;}  
            div.constraints .hljs{filter: saturate(1.5) brightness(1.5);/*filter: invert(0.6) contrast(8);border: 1px solid #6d6dff;*/}
            .hljs.for-constraints{filter: saturate(1.5) brightness(1.5);margin: 0px -15px;padding: 5px 20px;}
            div.constraints span.empty { display: block;overflow-x: auto;margin:0px 15px;padding: 0.5em;background: #595878;color: #dfdfdf;font-family: cursive;letter-spacing: 1.2px; }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/styles/default.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/highlight.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/languages/sql.min.js"></script>
        <script>hljs.initHighlightingOnLoad();</script>
    </head>
    <body>
        
        <?= $output ?>
        
        <button type='button' onClick='document.body.classList.toggle("only-codes")'>codes</button>
        <script>
        var html = '';
        document.querySelectorAll('code').forEach((code)=>{
            if(code.parentElement.classList.contains('constraints')){code.classList.add('for-constraints')}
        	html += code.outerHTML;
        	code.classList.remove('for-constraints')
        });
        var div = document.createElement('div');
        div.id='codes'
        div.innerHTML = html
        document.body.append(div)
        document.querySelectorAll('code').forEach((code)=>{
        	hljs.highlightBlock(code);
        });
        </script>
    </body>
</html>