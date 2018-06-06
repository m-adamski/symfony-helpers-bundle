# Helpers Bundle for Symfony 4

A collection of tools to improve the implementation of frequently-repeated functionalities:

* Breadcrumbs Helper
* Directory Helper
* Mailer Helper
* Notification Helper
* Pagination Helper
* PDF Helper

## Breadcrumbs Helper

The Breadcrumbs tool simplifies the generation and display process of breadcrumbs.

### How to use it?

In the controller function, generate the breadcrumbs structure:

```(php)
$this->breadcrumbsHelper->addRouteItem("Management", "administrator.index", [], "navigation");
$this->breadcrumbsHelper->addRouteItem("Data management", "administrator.data", ["id" => $id], "navigation");
```

To display breadcrumbs in Twig template file use ``breadcrumbs()`` function:

```(html)
<section class="breadcrumbs-container">
    {{ breadcrumbs() }}
</section>
```

### Functions

* addItem(string $text, string $url = "", string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true)
* addRouteItem(string $text, string $route, array $routeParameters = [], string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true)
* addNamespaceItem(string $namespace, string $text, string $url = "", string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true)
* prependItem(string $text, string $url = "", string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true)
* prependRouteItem(string $text, string $route, array $routeParameters = [], string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true)
* prependNamespaceItem(string $namespace, string $text, string $url = "", string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true)
* getNamespaceBreadcrumbs(string $namespace = self::DEFAULT_NAMESPACE)
* clear(string $namespace = "")

## Directory Helper

The Directory tool was created for faster directory management. With its help, you can quickly create a list of all files in a folder, create a new folder or generate a file path.

## Mailer Helper

As the name says, the Mailer Helper is used to send email messages. It has only one function:

* sendMessage(array $recipients, string $subject, string $template, array $data = [], array $attachments = [], string $sender = null, array $recipientsBCC = [], array $recipientsCC = [])

### How to use it?

In the controller function just call ``sendMessage`` function with parameters:

```(php)
$messageSubject = "This is example message";
$messageTemplate = "mail/Notification/notification.html.twig";

$messageData = [
    "template_parameter" => "Hello World!"
];

$messageAttachments = [
    $this->directoryHelper->generatePath([
        $this->directoryHelper->getPublicDirectory(), "download", "information.pdf"
    ], true)
];

// Send message
$this->mailerHelper->sendMessage(["john.sample@example.com"], $messageSubject, $messageTemplate, $messageData, $messageAttachments);
```

## Notification Helper

The tool is intended to help display information for the user.

### How to use it?

For example: An error occurred while creating a new blog entry and we would like to inform the user about it:

```(php)
$this->notificationHelper->addNotification(
    NotificationHelper::ERROR_TYPE,
    $this->translator->trans("An error occurred while trying to create a entry", [], "blog")
);
```

As in the case of Breadcrumbs Helper, we must also add a call to the corresponding function in the template:

```(html)
<section class="breadcrumbs-container">
    {{ notification() }}
</section>
```

### Functions

* addNotification(string $type, string $text)
* redirectNotification(string $url, string $type, string $text)
* routeRedirectNotification(string $route, string $type, string $text, array $routeParams = [])
* getNotifications()
* clear()

## Pagination Helper

The documentation will be completed.

## PDF Helper

The documentation will be completed.

## License

MIT