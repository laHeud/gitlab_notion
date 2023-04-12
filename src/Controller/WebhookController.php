<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use App\Service\NotionService;
use App\Service\GitlabService;


class WebhookController extends AbstractController
{

    private $logger;
    private $notion;
    private $gitlab;

    public function __construct(LoggerInterface $logger, NotionService $notion, GitlabService $gitlab)
    {
        $this->logger = $logger;
        $this->notion = $notion;
        $this->gitlab = $gitlab;
    }

    #[Route('/webhook/gitlab', name: 'app_webhook')]
    public function handleWebhook(Request $request): Response
    {
        $this->logger->debug('Webhook received', [
            'request' => $request->getContent(),
        ]);

        $data = json_decode($request->getContent(), true);

        // if ($data === null) {
        //     throw new \Exception('Bad JSON body from Stripe!');
        // }
        //$data = '{"object_kind":"merge_request","event_type":"merge_request","user":{"id":13783015,"name":"Clement Passevant","username":"Chapsou","avatar_url":"https://secure.gravatar.com/avatar/82c1657dc0fb4f1074c081d95c3ebd6e?s=80&d=identicon","email":"[REDACTED]"},"project":{"id":45064781,"name":"test-webhook","description":null,"web_url":"https://gitlab.com/unagi-games/test-webhook","avatar_url":null,"git_ssh_url":"git@gitlab.com:unagi-games/test-webhook.git","git_http_url":"https://gitlab.com/unagi-games/test-webhook.git","namespace":"Unagi","visibility_level":0,"path_with_namespace":"unagi-games/test-webhook","default_branch":"main","ci_config_path":"","homepage":"https://gitlab.com/unagi-games/test-webhook","url":"git@gitlab.com:unagi-games/test-webhook.git","ssh_url":"git@gitlab.com:unagi-games/test-webhook.git","http_url":"https://gitlab.com/unagi-games/test-webhook.git"},"object_attributes":{"assignee_id":null,"author_id":13783015,"created_at":"2023-04-11T09:38:46.773Z","description":"","head_pipeline_id":null,"id":216857690,"iid":1,"last_edited_at":null,"last_edited_by_id":null,"merge_commit_sha":null,"merge_error":null,"merge_params":{"force_remove_source_branch":"1"},"merge_status":"can_be_merged","merge_user_id":null,"merge_when_pipeline_succeeds":false,"milestone_id":null,"source_branch":"feature/test","source_project_id":45064781,"state_id":1,"target_branch":"main","target_project_id":45064781,"time_estimate":0,"title":"Test","updated_at":"2023-04-11T09:38:47.948Z","updated_by_id":null,"url":"https://gitlab.com/unagi-games/test-webhook/-/merge_requests/1","source":{"id":45064781,"name":"test-webhook","description":null,"web_url":"https://gitlab.com/unagi-games/test-webhook","avatar_url":null,"git_ssh_url":"git@gitlab.com:unagi-games/test-webhook.git","git_http_url":"https://gitlab.com/unagi-games/test-webhook.git","namespace":"Unagi","visibility_level":0,"path_with_namespace":"unagi-games/test-webhook","default_branch":"main","ci_config_path":"","homepage":"https://gitlab.com/unagi-games/test-webhook","url":"git@gitlab.com:unagi-games/test-webhook.git","ssh_url":"git@gitlab.com:unagi-games/test-webhook.git","http_url":"https://gitlab.com/unagi-games/test-webhook.git"},"target":{"id":45064781,"name":"test-webhook","description":null,"web_url":"https://gitlab.com/unagi-games/test-webhook","avatar_url":null,"git_ssh_url":"git@gitlab.com:unagi-games/test-webhook.git","git_http_url":"https://gitlab.com/unagi-games/test-webhook.git","namespace":"Unagi","visibility_level":0,"path_with_namespace":"unagi-games/test-webhook","default_branch":"main","ci_config_path":"","homepage":"https://gitlab.com/unagi-games/test-webhook","url":"git@gitlab.com:unagi-games/test-webhook.git","ssh_url":"git@gitlab.com:unagi-games/test-webhook.git","http_url":"https://gitlab.com/unagi-games/test-webhook.git"},"last_commit":{"id":"905cf3bb7021688d5755fc426aa745872e6e3ad5","message":"Testn","title":"Test","timestamp":"2023-04-11T11:38:29+02:00","url":"https://gitlab.com/unagi-games/test-webhook/-/commit/905cf3bb7021688d5755fc426aa745872e6e3ad5","author":{"name":"cpassevant-e","email":"clement.passevant@prowebce.com"}},"work_in_progress":false,"total_time_spent":0,"time_change":0,"human_total_time_spent":null,"human_time_change":null,"human_time_estimate":null,"assignee_ids":[],"reviewer_ids":[],"labels":[],"state":"opened","blocking_discussions_resolved":true,"first_contribution":false,"detailed_merge_status":"mergeable"},"labels":[],"changes":{},"repository":{"name":"test-webhook","url":"git@gitlab.com:unagi-games/test-webhook.git","description":null,"homepage":"https://gitlab.com/unagi-games/test-webhook"}}';


        // Afficher les données du webhook pour le débogage
        //$data = json_decode($data, true);


        $id = $this->gitlab->getIdInBranch($data);
        $url = $this->gitlab->getUrlInBranch($data);

        $notion = $this->notion->token();

        $databaseId = "410ad313-1241-4643-93e3-4d16ccb743b6";
        $database = $notion->databases()->find($databaseId);

        $result = $notion->databases()->query($database, $this->notion->queryProperty("PlusID", $id));

        $pages = $result->pages; // array of Page
        $result->hasMore; // true or false
        $result->nextCursor; // cursor ID or null

        $page = $notion->pages()->find($pages[0]->id);

        /** @var \Notion\Pages\Properties\RichTextProperty $property */
        $property = $page->getProperty("Gitlab");

        // Update property
        $updatedRelease = \Notion\Pages\Properties\Url::create($url);
        $uppage = $page->addProperty("Gitlab", $updatedRelease);

        // Send to Notion
        $notion->pages()->update($uppage);


        return new Response('OK');
    }
}
