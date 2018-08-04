<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 31/07/2018
 * Time: 9:41 AM
 */

namespace App\Makeflow;


use App\Makeflow\Dashboard\Entity\Workspace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;

class MakeflowManager
{
    /** @var string */
    protected $makeflowDir;

    /** @var Makeflow[] */
    protected $makeflows;

    /** @var EntityManagerInterface  */
    protected $entityManager;
    /** @var \Twig_Environment  */
    protected $twig;

    /**
     * MakeflowManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment $twig
     * @param string $makeflowDir
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, $makeflowDir = "")
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;

        if (!$makeflowDir) {
            $makeflowDir = __DIR__;
        }
        $this->makeflowDir = $makeflowDir;

        $this->initialize();
    }



    /**
     * @return \Generator
     */
    public function registerMakeflows()
    {
        $config = Yaml::parseFile($this->makeflowDir . '/makeflows.yaml');
        $contents = [];
        if (isset($config["makeflows"])) {
            $contents = $config["makeflows"];
        }
        foreach ($contents as $class) {
            yield new $class($this->entityManager, $this->twig);
        }
    }

    protected function initialize()
    {
        // init makeflows
        $this->makeflows = array();
        foreach ($this->registerMakeflows() as $makeflow) {
            if (!$makeflow instanceof Makeflow) {
                continue;
            }
            /** @var Makeflow $makeflow */
            $name = $makeflow->getName();
            if (isset($this->makeflows[$name])) {
                throw new \LogicException(sprintf('Trying to register two makeflow with the same name "%s"', $name));
            }
            $this->makeflows[$name] = $makeflow;
        }
    }

    /**
     * @return Makeflow[]
     */
    public function getMakeflows()
    {
        return $this->makeflows;
    }

    /**
     * @param $name
     * @return Makeflow
     * @inheritdoc
     */
    public function getMakeflow($name)
    {
        if (!isset($this->makeflows[$name])) {
            throw new \InvalidArgumentException(sprintf('Makeflow "%s" does not exist. Maybe you forgot to add it in the registerMakeflows() method of your %s.php file?', $name, get_class($this)));
        }

        return $this->makeflows[$name];
    }

    /**
     * @param Workspace $workspace
     * @return Makeflow|null
     */
    public function getMakeflowByWorkspace(Workspace $workspace)
    {
        $makeflow = null;
        foreach ($this->makeflows as $name => $mayExpectMakeflow)
        {
            if ($mayExpectMakeflow->getName() === $workspace->getMakeflowName()) {
                $makeflow = $mayExpectMakeflow;
                break;
            }
        }
        return $makeflow;
    }


}