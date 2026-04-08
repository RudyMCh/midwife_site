<?php

namespace App\Command;

use App\Services\Tools;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class MakeAdminController extends Command
{
    protected static $defaultName = 'make:admin-controller';


    public function __construct( private readonly Tools $tools)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->setDescription('Create Admin Controller')
            ->setHelp('Create controller for admin')
            ->addArgument('entityName', InputArgument::REQUIRED, 'Nom de l\'entité')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $className = ucfirst((string) $input->getArgument('entityName'));
        $lowerClassName = lcfirst($className);
        $instance = '$'.lcfirst($className);
        $snakeName = Str::asRouteName($className);
        $controllerDir = __DIR__ . '/../Controller/';
        $formDir = __DIR__ . '/../Form/';
        $handlerDir = __DIR__ . '/../Form/Handler/';

        if(!file_exists($formDir) && !mkdir($formDir, 0777) && !is_dir($formDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $formDir));
        }

        if(!file_exists($handlerDir) && !mkdir($handlerDir, 0777) && !is_dir($handlerDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $handlerDir));
        }

        $metadatas = $this->tools->getProperties("App\Entity\\".$className);
        $tabResponse = [];

        foreach ($metadatas as  $metadata){
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Voulez vous ajoutez '.$metadata.' dans les fields ? (default : true) ', true);
            if ($helper->ask($input, $output, $question)){
                $tabResponse[$metadata]['fields'] = true;
                $tabResponse[$metadata]['show'] = $helper->ask($input, $output , new Question('Quel nom afficher pour la propriété? (default : '.$metadata.')  ', $metadata));
            }
        }

        $fields = "";
        foreach ($tabResponse as $key => $item){
            $fields.=$item['fields'] ? "'".ucfirst((string) $item['show'])."' => '".ucfirst($key)."',\n" : "";
        }
        $controllerContent =
            "<?php
namespace App\Controller\AdminController;

use App\Entity\\".$className.";
use App\Form\\".$className."Type;
use App\Form\Handler\\".$className."Handler;
use App\Repository\\".$className."Repository;
use App\Services\Tools;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ".$className."Controller
 * @package App\Controller\AdminController
 * @Route(\"/admin/$lowerClassName\", name=\"admin_".$snakeName."_\")
 */
class ".$className."Controller extends AbstractController
{
    private EntityManagerInterface \$entityManager
    public function __construct(EntityManagerInterface \$entityManager)
    {
        $this->entityManager = \$entityManager;
    }

    /**
     * @Route(\"/\", name=\"index\", methods={\"GET\"})
     * @param ".$className."Repository ".$instance."Repository
     * PaginatorInterface \$paginator
     * Request \$request
     */
    public function index(".$className."Repository ".$instance."Repository, PaginatorInterface \$paginator, Request \$request): Response
    {
        \$els = \$paginator->paginate(
            ".$instance."Repository->createQueryBuilder('a')->getQuery(),
            \$request->query->getInt('page', 1),
            10
        );
        return \$this->render('admin/crud/index.html.twig', [
            'els'=>\$els,
            'paginator'=>false,
            'search'=>false,
            'class'=> ".$className."::class,
            'route'=> 'admin_". $snakeName ."',
            'breadcrumb'=>[
                [
                    'text'=>'tous les éléments'
                ]
            ],
            'fields' => [
                $fields
            ],
            'title' => 'Tous les élements',
            'add_button_label'=>'Ajouter un élément'
        ]);
    }

    /**
     * @Route(\"/new\", name=\"new\", methods={\"GET\",\"POST\"})
     * @param Request \$request
     * @param ".$className."Handler ".$instance."Handler
     * @param Tools \$tools
     * @return Response
     */
    public function new(Request \$request, Tools \$tools, ".$className."Handler ".$instance."Handler): Response
    {
        $instance = new ".$className."();
        \$form = \$this->createForm(".$className."Type::class, $instance);
        if (".$instance."Handler->new(\$form, \$request)) {
            return \$this->redirectToRoute('admin_".$snakeName."_index');
        }

        return \$this->render('admin/crud/_form.html.twig', [
            'form'=>\$form->createView(),
            'el'=>".$instance.",
            'button_label'=>'Créer',
            'route'=>'admin_". $snakeName."',
            'title'=>'Ajouter un élément',
            'breadcrumb'=>[
                [
                    'route'=>'admin_". $snakeName ."_index',
                    'text'=>'tous les éléments'
                ],
                [
                    'text'=>'ajouter un élément'
                ]
            ],

        ]);
    }

    /**
     * @Route(\"/edit/{id}\", name=\"edit\")
     * @param Request \$request
     * @param ".$className." ".$instance."
     * @param Tools \$tools
     * @return Response
     */
    public function edit(Request \$request, ".$className." ".$instance.", Tools \$tools): Response
    {
        \$form = \$this->createForm(".$className."Type::class, ".$instance.");
        if (".$instance."Handler->edit(\$form, \$request)) {
            return \$this->redirectToRoute('admin_".$snakeName."_edit', ['id'=>".$instance."->getId()]);
        }
        return \$this->render('admin/crud/_form.html.twig', [
            'el' => ".$instance.",
            'route'=> 'admin_".$snakeName."',
            'form' => \$form->createView(),
            'button_label' => 'Mettre à jour',
            'title' => 'Edition',
            'breadcrumb'=>[
                [
                    'route'=>'admin_".$snakeName."_index',
                    'text'=>'".$lowerClassName."s'
                ],
                [
                    'text'=>'édition '
                ]
            ],
        ]);
    }

    /**
     * @Route(\"/{id}\", name=\"delete\", methods={\"DELETE\"})
     * @param Request \$request
     * @return Response
     */
    public function delete(Request \$request,".$className." ".$instance."): Response
    {
        if (\$this->isCsrfTokenValid('delete'.".$instance."->getId(), \$request->request->get('_token'))) {
            \$this->entityManager->remove(".$instance.");
            \$this->entityManager->flush();
        }
        return \$this->redirectToRoute('admin_".$snakeName."_index');
    }
}";


        $command = $this->getApplication()->find('make:form');

        $arguments = [
            'name'    => $className,
        ];

        $greetInput = new ArrayInput($arguments);
        try {
            $command->run($greetInput, $output);
        } catch (\Exception) {
        }

        if(!file_exists($controllerDir.$className."Controller.php")){
            $output->writeln([
                'Création du fichier '.$controllerDir.$className.'Controller.php ...'
            ]);
            file_put_contents($controllerDir.$className.'Controller.php', $controllerContent);
            $output->writeln([
                'Le dossier a bien été créé'
            ]);
        }

        $handlerContent =
            "<?php
namespace App\Form\Handler;

use App\Entity\\".$className.";
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class ".$className."Handler extends AbstractController
{
    private \$entityManager;

    public function __construct(EntityManagerInterface \$entityManager)
    {
        \$this->entityManager = \$entityManager;
    }

    public function new(FormInterface \$form, Request \$request): bool
    {
        \$form->handleRequest(\$request);
        if (\$form->isSubmitted() && \$form->isValid()) {
            ".$instance." = \$form->getData();
            \$this->entityManager->persist(".$instance.");
            \$this->entityManager->flush();
            return true;
        }
        return false;
    }

    public function edit(FormInterface \$form, Request \$request): bool
    {
        \$form->handleRequest(\$request);
        if (\$form->isSubmitted() && \$form->isValid()) {
            \$this->entityManager->flush();
            return true;
        }
        return false;
    }
}
";
        if(!file_exists($handlerDir.$className."Handler.php")){
            $output->writeln([
                'Création du fichier '.$handlerDir.$className.'Handler.php ...'
            ]);
            file_put_contents($handlerDir.$className.'Handler.php', $handlerContent);
            $output->writeln([
                'Le dossier a bien été créé'
            ]);
        }
    }
}