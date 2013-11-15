<?php

namespace SymfonyContrib\Bundle\FileFieldBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SymfonyContrib\Bundle\FileFieldBundle\Event\UploadPreMoveEvent;
use SymfonyContrib\Bundle\FileFieldBundle\Event\UploadMoveEvent;
use SymfonyContrib\Bundle\FileFieldBundle\Event\UploadPostMoveEvent;

class UploadController extends Controller
{
    public function uploadAction(Request $request)
    {
        $files = $request->files->all();

        // Fire the pre-move event.
        $preMoveEvent = new UploadPreMoveEvent($files);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch('filefield.upload.pre_move', $preMoveEvent);

        $session = $this->get('session');
        $helper  = $this->get('filefield.upload_helper');

        $responses = [];
        foreach ($files as $formId => $file) {
            $ns = 'filefield/' . $formId . '/';
            $uploadDir = $session->get($ns . 'uploadDir', null);
            $uri = $session->get($ns . 'uri', null);

            $response = null;
            if ($preMoveEvent->isDefaultMoveAllowed()) {
                if (empty($uploadDir) || empty($uri)) {
                    throw new \Exception('Missing uploadDir or uri.');
                }

                $name = $file->getClientOriginalName();
                $size = $file->getSize();
                $mime = strtolower($file->getMimeType());
                $icon = $helper->getFileIcon($mime);

                // Move file(s) to permanent location.
                $file->move($uploadDir, $name);

                // Build a ajax response.
                $response = [
                    'name' => $name,
                    'size' => $helper->formatSize($size),
                    'mime' => $mime,
                    'url' => $uri . $name,
                    'iconUri' => $helper->getIconUri() . $icon,
                ];
            }

            // Fire the move event.
            $moveEvent = new UploadMoveEvent($file, $uploadDir, $uri, $response);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch('filefield.upload.move', $moveEvent);

            // Get the final file response.
            $response = $moveEvent->getResponse();

            if (empty($response) || !is_array($response)) {
                throw new \Exception('Invalid upload response.');
            }

            $responses[] = $response;
        }

        // Fire the post-move event.
        $postMoveEvent = new UploadPostMoveEvent($files, $responses);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch('filefield.upload.post_move', $postMoveEvent);

        return new JsonResponse(['files' => $postMoveEvent->getResponse()]);
    }


}
