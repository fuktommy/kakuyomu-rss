<?php
/*
 * Copyright (c) 2012,2021 Satoshi Fukutomi <info@fuktommy.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHORS AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHORS OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */
namespace Fuktommy\Kakuyomu;

require_once __DIR__ . '/../libs/Fuktommy/Bootstrap.php';
use Fuktommy\Bootstrap;
use Fuktommy\Kakuyomu\Model\WorkApi;
use Fuktommy\WebIo\Action;
use Fuktommy\WebIo\Context;


class IndexAction implements Action
{
    public function execute(Context $context)
    {
        $api = new WorkApi($context->getResource());
        $workId = $context->get('get', 'work');

        if (! in_array($workId, $context->config['works'])) {
            $context->putHeader('HTTP/1.1 404 Not Found');
            return;
        }
        $work = $api->get($workId);

        $smarty = $context->getSmarty();
        $smarty->assign('config', $context->config);
        $smarty->assign('workId', $workId);
        $smarty->assign('work', $work);
        $smarty->display('atom.tpl');
    }
}


(new Controller())->run(new IndexAction(), Bootstrap::getContext());
