<?php
namespace YourCompanyModule;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;

class Module
{
    // Метод "init" вызывается при запуске приложения и  
    // позволяет зарегистрировать обработчик событий.
    public function init(ModuleManager $manager)
    {
        // Получаем менеджер событий.
        $eventManager = $manager->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Регистрируем метод-обработчик. 
        $sharedEventManager->attach(__NAMESPACE__, 'route', 
                                    [$this, 'onRoute'], 100);
    }
    
    // Обработчик события.
    public function onRoute(MvcEvent $event)
    {
        if (php_sapi_name() == "cli") {
            // Не выполняем перенаправление на HTTPS в консольном режиме.
            return;
        }
        
        // Получаем URI запроса
        $uri = $event->getRequest()->getUri();
        $scheme = $uri->getScheme();
        // Если схема - не HTTPS, перенаправляем на тот же URI, но
        // со схемой HTTPS.
        if ($scheme != 'https'){
            $uri->setScheme('https');
            $response=$event->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $uri);
            $response->setStatusCode(301);
            $response->sendHeaders();
            return $response;
        }
    }
    
    // ...
}
