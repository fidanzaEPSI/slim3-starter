<?php


namespace App\Controllers\Articles;

use App\Controllers\Controller;
use App\Database\Transformers\ArticleTransformer;
use App\Models\Article;
use App\Validation\{Forms\ArticleForm, Validator};
use League\Fractal\Resource\{Collection, Item};
use Slim\Http\{Request, Response};

class ArticleController extends Controller
{
    /**
     * @apiGroup Articles
     * @apiName index
     * @api {get} /article
     * @apiSuccess (200) {String[]}     articles            List of articles.
     * @apiSuccess {Int}                article.id          Article ID.
     * @apiSuccess {String}             article.title       The article's title.
     * @apiSuccess {String}             article.body        The article's body.
     * @apiSuccess {String}             article.published   Since when the article was published/created.
     * @apiSuccess {String}             article.updated     Since when the article was updated.
     * @apiDescription Get all articles
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * "data": {
     *          "id": 1,
     *          "title": "Title 1",
     *          "body": "My first article",
     *          "published": "2 days before",
     *          "updated": "2 days before"
     *      },
     *      {
     *          "id": 1,
     *          "title": "Title 2",
     *          "body": "My second article",
     *          "published": "3 days before",
     *          "updated": "1 days before"
     *      },
     * }
     */

     /**
     * @param Response $response
     * @return Response
     */
    public function index(Response $response): Response
    {
        $articles = Article::all();
        $resource = new Collection($articles, new ArticleTransformer);
        $data = $this->fractal->createData($resource)->toArray();
        return $response->withJson($data, 200);
    }


    /**
     * @apiGroup Articles
     * @apiName show
     * @api {get} /article/:id
     * @apiParam {Number} id Articles unique ID.
     * @apiSuccess {Int}                article.id          Article ID.
     * @apiSuccess {String}             article.title       The article's title.
     * @apiSuccess {String}             article.body        The article's body.
     * @apiSuccess {String}             article.published   Since when the article was published/created.
     * @apiSuccess {String}             article.updated     Since when the article was updated.
     * @apiDescription Get an article by ID.
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * "data": {
     *      "id": 1,
     *      "title": "Title 1",
     *      "body": "My first article",
     *      "published": "2 days before",
     *      "updated": "2 days before"
     * }
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 404 Not Found
     * {
     *   "error": "Record was not found"
     * }
     */

     /**
     * @param Response $response
     * @param int $id
     * @return Response
     */
    public function show(Response $response, int $id): Response
    {
        $article = Article::find($id);
        if(!$article) {
            return $response->withJson(['error' => 'Record was not found'], 404);
        }

        $resource = new Item($article, new ArticleTransformer);
        $data = $this->fractal->createData($resource)->toArray();
        return $response->withJson($data, 200);
    }

    public function store(Request $request, Response $response, Validator $validator)
    {
        $validator->validate($request,ArticleForm::getRules());
        if($validator->fails()) {
            return $response->withJson(['errors' => $this->session->get('errors')], 400);
        }
        $article = Article::create($request->getParams());
        return $response->withJson($article, 200);
    }

    public function delete(Request $request, Response $response): Response
    {
        die('Delete');
    }
}