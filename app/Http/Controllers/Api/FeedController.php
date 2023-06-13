<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\ArticleResource;

class FeedController extends Controller
{
    use ApiResponseTrait;


    public function getFeed(Request $request){

        $validator = Validator::make(
            $request->all(),
            [

                'page' => 'nullable|numeric',
                'from_date' => 'nullable|date|date_format:YYYY-MM-DD',
                'to_date' => 'nullable|date|date_format:YYYY-MM-DD',
                'source' => 'nullable|string',
                'author' => 'nullable|string',
                'category' => 'nullable|string',
                'sort_by' => 'nullable|string'

            ],
           
        );

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), null, 400);
        }


        $newsApi = $this->getFromNewsApi($request->page ?? 1)['articles'];
        $allArticles = [...$newsApi];
        $response = ArticleResource::collection($allArticles);

        return $this->apiSuccessResponse('News feed', $response);
    }


    private function getFromNewsApi($page){
        // https://newsapi.org/v2/everything?q=tesla&from=2023-05-11&sortBy=publishedAt&apiKey=93d2dc0d2b9741d6b615f46343872314

        $response = Http::get("https://newsapi.org/v2/top-headlines?sources=abc-news,bbc-news,associated-press,bleacher-report&pageSize=10&page=".$page."&sortBy=popularity&apiKey=93d2dc0d2b9741d6b615f46343872314");


        return $response->collect();
    }

    private function getFromTheGuardian(){
        // https://newsapi.org/v2/everything?q=tesla&from=2023-05-11&sortBy=publishedAt&apiKey=93d2dc0d2b9741d6b615f46343872314

        $response = Http::get('https://newsapi.org/v2/top-headlines?sources=bbc-news&from=2023-05-11&sortBy=popularity&apiKey=93d2dc0d2b9741d6b615f46343872314');


        return $response->collect();
    }

}
