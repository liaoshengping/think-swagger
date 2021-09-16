<?php


namespace Liaosp\ThinkSwagger;


use think\facade\Env;
use think\Request;

class Controller extends \think\Controller
{

    /**
     * Swagger Api json
     */
    public function apidocJson()
    {
        //绝对路径
        $path = config('app.swagger_path');

        if (!$path){
            $path = Env::get('app_path');
        }
        $openapi = \OpenApi\Generator::scan([$path]);
        echo $openapi->toJSON();
    }


    /**
     * 创建
     */
    public function apidoc()
    {

        $public_target_path =app()->getRootPath() . 'public';

        $resources = [
            '/swagger/swagger-ui.css',
            '/swagger/swagger-ui-bundle.js',
            '/swagger/swagger-ui-standalone-preset.js'
        ];

        foreach ($resources as $resource) {
            if(is_file($public_target_path.$resource)){
                continue;
            }
            if (!is_dir($public_target_path.'/swagger')){
                mkdir($public_target_path.'/swagger','0777');
            }
            file_put_contents($public_target_path.'/'.$resource,file_get_contents(__DIR__.'/..'.$resource));
        }


        $request = \think\facade\Request::instance();
        $domain = $request->domain();
        $html = <<<HTML
<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>接口文档</title>
    <link rel="stylesheet" type="text/css" href="/swagger/swagger-ui.css" />

    <style>
      html
      {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
      }

      *,
      *:before,
      *:after
      {
        box-sizing: inherit;
      }

      body
      {
        margin:0;
        background: #fafafa;
      }
    </style>
  </head>

  <body>
    <div id="swagger-ui"></div>

    <script src="/swagger/swagger-ui-bundle.js" charset="UTF-8"> </script>
    <script src="/swagger/swagger-ui-standalone-preset.js" charset="UTF-8"> </script>
    <script>
    window.onload = function() {
      // Begin Swagger UI call region
      const ui = SwaggerUIBundle({
        url: "{$domain}/apidocJson",
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],
        plugins: [
          SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "StandaloneLayout"
      });
      // End Swagger UI call region

      window.ui = ui;
    };
  </script>
  </body>
</html>

HTML;
        echo $html;

    }


}