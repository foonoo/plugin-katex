<?php

namespace foonoo\plugins\foonoo\katex;

use foonoo\Plugin;
use foonoo\events\ContentLayoutApplied;
use foonoo\events\AssetPipelineReady;

class KatexPlugin extends Plugin {

    public function getEvents() {
        return [
            ContentLayoutApplied::class => fn(ContentLayoutApplied $e) => $this->injectCode($e),
            AssetPipelineReady::class => fn(AssetPipelineReady $e) => $this->injectAssets($e)
        ];
    }

    private function injectAssets(AssetPipelineReady $e) {
        $assetPipeline = $e->getAssetPipeline();
        $options = ['base_directory' => __DIR__ . "/assets"];
        $assetPipeline->addItem("katex.css", "css", $options);
        $assetPipeline->addItem("katex.js", "js", $options);
        $assetPipeline->addItem("auto-render.js", "js", $options);
        $assetPipeline->addItem("css", "files", $options);
        
    }

    private function injectCode(ContentLayoutApplied $e) {
        $dom = $e->getDOM();
        if($dom !== null) {
            $xpath = new \DOMXPath($dom);
            $headTag = $xpath->query("//head")->item(0);
            $scriptTag = $dom->createElement("script", 
            <<<JS
            window.addEventListener('load', () => renderMathInElement(document.body, 
                {
                    delimiters:[
                        {left: "$$", right: "$$", display: true},
                        {left: "$", right: "$", display: false}
                    ]
                }
            ))
            JS); //"window.addEventListener('load', () => renderMathInElement(document.body))");
            $headTag->appendChild($scriptTag);            
        }       
    }

}