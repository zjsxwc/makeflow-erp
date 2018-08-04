<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 01/08/2018
 * Time: 8:38 AM
 */

namespace App\Makeflow;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateMakeflowCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('makeflow:create')
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new makeflow skeleton.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a new makeflow skeleton\neg: makeflow:create PaoMianMakeflow')
            ->addArgument('makeflow-name', InputArgument::OPTIONAL, 'The name of the makeflow. suffixed by `Makeflow`');
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        $makeflowNameQuestion = new Question('Please choose a makeflow name(suffixed by `Makeflow`): ');
        $makeflowNameQuestion->setValidator(function ($makeflowName) {
            if (empty($makeflowName)) {
                throw new \Exception('Makeflow name can not be empty');
            }

            $suffixLength = strlen("Makeflow");
            $isSuffixValid = (substr($makeflowName, -$suffixLength) === "Makeflow");
            if (!$isSuffixValid) {
                throw new \Exception('Makeflow name must suffixed by Makeflow');
            }

            return $makeflowName;
        });
        $mayValidMakeflowName = $input->getArgument('makeflow-name');
        $validator = $makeflowNameQuestion->getValidator();
        try {
            $validMakeflowName = $validator($mayValidMakeflowName);
        } catch (\Exception $e) {
            $validMakeflowName = $this->getHelper('question')->ask($input, $output, $makeflowNameQuestion);
        }
        $input->setArgument('makeflow-name', $validMakeflowName);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Makeflow name: ' . $input->getArgument('makeflow-name'));

        $makeflowName = $input->getArgument('makeflow-name');
        /** @var string[] $places */
        $places = [];
        $placeQuestion = new Question('Please choose a place name(suffixed by `Place` or press <return> to stop): ');
//        $placeQuestion->setAutocompleterValues(["DemoPlace"]);
        $placeQuestion->setValidator(function ($placeName) {
            if (!$placeName) {
                return "";
            }
            $suffixLength = strlen("Place");
            $isSuffixValid = (substr($placeName, -$suffixLength) === "Place");
            if (!$isSuffixValid) {
                throw new \Exception('Makeflow name must suffixed by Place');
            }

            return $placeName;
        });

        while (true) {
            $placeName = $this->getHelper('question')->ask($input, $output, $placeQuestion);
            if (!$placeName) {
                break;
            }
            $places[] = $placeName;
        }

        $makeflowPath = __DIR__ . "/" . $makeflowName;
        mkdir($makeflowPath);
        foreach (["Entity", "Place", "Repository", "View"] as $subDirName) {
            mkdir($makeflowPath . "/" . $subDirName);
        }

        list($makeflowPropertyName) = explode("Makeflow", $makeflowName);
        $PaoMianMakeflow_php = <<<EOT
<?php
namespace App\Makeflow\\$makeflowName;

use App\Makeflow\Makeflow;

class $makeflowName extends Makeflow
{
    protected \$name = "$makeflowPropertyName";
}
EOT;
        file_put_contents($makeflowPath . "/" . $makeflowName . ".php", $PaoMianMakeflow_php);
        file_put_contents($makeflowPath . "/makeflow.yaml", "");

        $configuration_yaml = <<<EOT
doctrine:
    orm:
        mappings:
            $makeflowName:
                is_bundle: false
                type: annotation
                dir: '%kernel.project_dir%/src/Makeflow/$makeflowName/Entity'
                prefix: 'App\Makeflow\\$makeflowName\Entity'

twig:
    paths:
        - { namespace: $makeflowName, value: '%kernel.project_dir%/src/Makeflow/$makeflowName/View' }

    debug: '%kernel.debug%'
    strict_variables: '%kernel.debug%'
EOT;
        file_put_contents($makeflowPath . "/configuration.yaml", $configuration_yaml);

        foreach ($places as $placeName) {

            $Place_php = <<<EOT
<?php
namespace App\Makeflow\\$makeflowName\Place;

use App\Makeflow\Place;
use Symfony\Component\HttpFoundation\Request;

class $placeName extends Place
{

    public function processAction(Request \$request)
    {
        //return \$this->render("xxx.html.twig", []);
    }

}
EOT;

            file_put_contents($makeflowPath . "/Place/" . $placeName . ".php", $Place_php);

        }

        $output->writeln('Finished!');
    }
}