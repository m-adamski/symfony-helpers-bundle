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

As the name says, the Mailer Helper is used to send email messages. Helper contain two functions:

* buildMessage(?string $subject = null, ?string $body = null, ?string $contentType = null, ?string $charset = null)
* sendMessage(MailerMessage $mailerMessage, ?array &$failedRecipients = null)

The first function generates an instance of the MailerMessage class, which extends the basic class Swift_Message. 
The other one tries to send the generated message.

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

To be able to use Pagination Helper, several changes are required in both the controller and the entity repository. Sample controller function:

```(php)
public function index(Request $request, int $page) {
    return $this->render("modules/News/index.html.twig", [
        "page" => $page,
        "news" => $this->paginationHelper->responseData($request, $this->newsRepository, $page)
    ]);
}
```

As you can see the ``responseData`` function requires three parameters. One of them is the repository of the entity we are paging. Now we need to make changes in the entity repository - repository class have to extends ``Adamski\Symfony\HelpersBundle\Model\PaginableRepository`` and implement ``getPaginated`` function:

```(php)
public function getPaginated(int $page = 1, int $limit = 20) {
    $queryBuilder = $this->getEntityManager()->getRepository("App:News")
        ->createQueryBuilder("news");

    $queryBuilder->where($queryBuilder->expr()->eq("news.public", true))
        ->orderBy("news.createdAt", "DESC");

    return $this->paginate($queryBuilder->getQuery(), $page, $limit);
}
```

Now just add function to render the pagination component in template.

```
{{ pagination(news, "news.index", page) }}
```

## PDF Helper

PDF Helper offers only one function - ``initDocument``. This function create PDFDocument object which provides some useful functions needed to create a PDF document. Below is an example of using PDF Helper:

```(php)
$pdfName = "Sample PDF document";

// Generate PDF template
$documentContent = $this->renderView("pdf/document-content.html.twig", [
    "data" => $data
]);

// Generate PDF document
$pdfDocument = $this->pdfHelper->initDocument();
$pdfDocument->setTitle($pdfName);
$pdfDocument->setAuthor("Author");
$pdfDocument->setCreator("Creatot");
$pdfDocument->setFooter($pdfName);
$pdfDocument->writeHTML($documentContent);

// Generate response
$response = new Response(
    $pdfDocument->output($pdfName)
);

$response->headers->set("Content-Type", "application/pdf");
$response->headers->set("Content-Disposition", "attachment; filename=\"" . $pdfName . ".pdf\"");

return $response;
```

In the example shown, the template file is rendered, and then the HTML code is inserted as the content of the new PDF document. In addition, the parameters of the PDF file are set.

## License

MIT
