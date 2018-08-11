<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 02/08/2018
 * Time: 1:32 PM
 */

namespace App\Makeflow;


class MakeflowConfigGraphGenerator
{

    protected $makefileConfigKeyMap = [];
    protected $levelKeyMap = [];

    public function calculatePlaceLevel($placeName, $makefileConfigKey)
    {
        if (isset($this->levelKeyMap[$makefileConfigKey][$placeName])) {
            return $this->levelKeyMap[$makefileConfigKey][$placeName];
        }
        $makefileConfig = $this->makefileConfigKeyMap[$makefileConfigKey];
        if (!$makefileConfig[$placeName]) {
            $this->levelKeyMap[$makefileConfigKey][$placeName] = 1;
        } else {
            $maxLevel = 1;
            foreach ($makefileConfig[$placeName] as $prerequisitePlaceName) {
                $prerequisiteLevel = $this->calculatePlaceLevel($prerequisitePlaceName, $makefileConfigKey);
                if ($prerequisiteLevel > $maxLevel) {
                    $maxLevel = $prerequisiteLevel;
                }
            }
            $this->levelKeyMap[$makefileConfigKey][$placeName] = $maxLevel + 1;
        }

        return $this->levelKeyMap[$makefileConfigKey][$placeName];
    }


    public function parseMakeflowConfig($makefileConfig)
    {
        $key = uniqid();
        $this->makefileConfigKeyMap[$key] = $makefileConfig;
        $this->levelKeyMap[$key] = [];

        foreach ($makefileConfig as $placeName => $prerequisitePlaceNames) {
            $this->calculatePlaceLevel($placeName, $key);
        }

        $placeLevels = $this->levelKeyMap[$key];

        $directedLines = [];
        foreach ($makefileConfig as $placeName => $prerequisitePlaceNames) {
            foreach ($prerequisitePlaceNames as $prerequisitePlaceName) {
                $line = [
                    "from" => $prerequisitePlaceName,
                    "to" => $placeName
                ];
                $directedLines[] = $line;
            }
        }

        return [
            "placeLevels" => $placeLevels,
            "directedLines" => $directedLines
        ];
    }


    /**
     * @param Makeflow $makeflow
     * @param null $points
     * @param null $directedLines
     * @param [] $svgData
     * @return string
     */
    public function generateSvg($makeflow, &$points = null, &$directedLines = null, &$svgData = [])
    {
        $makefileConfig = $makeflow->getMakeflowConfig();
        $places = $makeflow->getPlaces();
        $makeflowName = $makeflow->getName();
        $makeflowLabel = $makeflow->getLabel();
        $parsedData = $this->parseMakeflowConfig($makefileConfig);
        $placeLevels = $parsedData["placeLevels"];
        $directedLines = $parsedData["directedLines"];

        $points = [];
        $y = [];
        $maxY = 1;
        $maxX = 1;
        $maxPlaceNameLength = 0;
        /** @var string[][] $placeNamesByLevel */
        $placeNamesByLevel = [];
        foreach ($placeLevels as $placeName => $placeLevel) {
            if (isset($placeNamesByLevel[$placeLevel])) {
                $placeNamesByLevel[$placeLevel][] = $placeName;
            } else {
                $placeNamesByLevel[$placeLevel] = [$placeName];
            }

            if ($maxPlaceNameLength < mb_strlen($placeName)) {
                $maxPlaceNameLength = mb_strlen($placeName);
            }
            $points[$placeName] = [
                "placeName" => $placeName,
                "placeLabel" => $places[$placeName]->getLabel(),
                "placeDescription" => $places[$placeName]->description,
                "level" => $placeLevel,
                "x" => $placeLevel,
                "canVisit" => $places[$placeName]->canVisit
            ];
            if ($maxX < $placeLevel) {
                $maxX = $placeLevel;
            }

            if (isset($y[$placeLevel])) {
                $count = count($y[$placeLevel]);
                $points[$placeName]["y"] = $count + 1;
                $y[$placeLevel][] = $points[$placeName];
                if ($maxY < $points[$placeName]["y"]) {
                    $maxY = $points[$placeName]["y"];
                }
            } else {
                $points[$placeName]["y"] = 1;
                $y[$placeLevel] = [$points[$placeName]];
            }
        }

        //重新确定point的y值，不让线重合
        ksort($placeNamesByLevel);
        foreach ($placeNamesByLevel as $level => $placeNames) {
            foreach ($placeNames as $placeName) {
                $point = $points[$placeName];

                $linesToPoint = [];
                foreach ($directedLines as $directedLine) {
                    $toPoint = $points[$directedLine["to"]];

                    if ($toPoint["placeName"] === $placeName) {
                        $linesToPoint[] = $directedLine;
                    }
                }
                if ($linesToPoint) {

                    $isNeedToTryY = true;
                    while ($isNeedToTryY) {
                        $isAllTanDiff = true;
                        $allTan = [];
                        foreach ($linesToPoint as $directedLine) {
                            $fromPoint = $points[$directedLine["from"]];
                            $tan = (floatval($points[$placeName]["y"]) - floatval($fromPoint["y"]))/(floatval($points[$placeName]["x"]) - floatval($fromPoint["x"]));
                            if (in_array($tan, $allTan)) {
                                $isAllTanDiff = false;
                                break;
                            } else {
                                $allTan[] = $tan;
                            }
                        }
                        if ($isAllTanDiff) {
                            $isNeedToTryY = false;
                        } else {
                            $points[$placeName]["y"] += 0.3;
                        }
                    }
                    if ($maxY < $points[$placeName]["y"]) {
                        $maxY = $points[$placeName]["y"];
                    }
                }


            }
        }



        $pointSvgList = [];
        $interval = $maxPlaceNameLength * 10;
        $round = 15;
        $svgData["places"] = [];
        foreach ($points as $point) {
            $color = "#33BBFF";
            $label = ($point["placeLabel"]?$point["placeLabel"]:$point["placeName"]);
            $pointSvgList[] = sprintf(
                '
<circle cx="%s" cy="%s" r="%s" stroke="black" stroke-width="2" fill="%s" makeflow-name="%s" place-name="%s" class="makeflow-place-circle"/>
<text x="%s" y="%s" fill="#581845">%s</text>
',
                $point["x"] * $interval,
                $point["y"] * $interval,
                $round,
                $color,
                $makeflowName,
                $point["placeName"],
                $point["x"] * $interval - mb_strlen($label) * 5,
                $point["y"] * $interval + $interval / 3,
                $label
            );

            $svgData["places"][] = [
                "circleX" => $point["x"] * $interval,
                "circleY" => $point["y"] * $interval,
                "circleRound" => $round,
                "color" => $color,
                "makeflowName" => $makeflowName,
                "makeflowLabel" => $makeflowLabel,
                "placeName" => $point["placeName"],
                "textX" =>  $point["x"] * $interval - mb_strlen($label) * 5,
                "textY" =>  $point["y"] * $interval + 30,
                "label" => $label,
                "placeLabel" => $point["placeLabel"],
                "placeDescription" => $point["placeDescription"],
                "placeCanVisit" => $point["canVisit"]
            ];
        }
        $pointSvg = implode("", $pointSvgList);

        $lineSvgList = [];
        $svgData["directedLines"] = [];
        foreach ($directedLines as $directedLine) {
            $fromPoint = $points[$directedLine["from"]];
            $toPoint = $points[$directedLine["to"]];

            $x1 = $fromPoint["x"] * $interval;
            $y1 = $fromPoint["y"] * $interval;
            $x2 = $toPoint["x"] * $interval;
            $y2 = $toPoint["y"] * $interval;

            list($lineStartX, $lineStartY) = $this->calculateCircleLineStartPoint($x1, $y1, $x2, $y2, $round);
            list($lineEndX, $lineEndY) = $this->calculateCircleLineEndPoint($x1, $y1, $x2, $y2, $round);
            $smallRound = 1.8;
            $lineSvgList[] = sprintf(
                '
<line x1="%s" y1="%s" x2="%s" y2="%s" style="stroke:rgb(99,99,99);stroke-width:2" from-place-name="%s" to-place-name="%s" />
<circle cx="%s" cy="%s" r="%s" stroke="black" stroke-width="2" fill="black"/>

',
                $lineStartX,
                $lineStartY,
                $lineEndX,
                $lineEndY,
                $fromPoint["placeName"],
                $toPoint["placeName"],
                $lineEndX,
                $lineEndY,
                $smallRound
            );


            $svgData["directedLines"][] = [
                "lineStartX" => $lineStartX,
                "lineStartY" => $lineStartY,
                "lineEndX" => $lineEndX,
                "lineEndY" => $lineEndY,
                "smallRound" => $smallRound
            ];

        }
        $lineSvg = implode("", $lineSvgList);


        $width = ($maxX + 1) * $interval;
        $height = ($maxY + 1) * $interval;
        $svgTemplate = <<<EOT
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="$width" height="$height">
   $pointSvg
   $lineSvg
</svg>
EOT;

        $svgData["width"] = $width;
        $svgData["height"] = $height;

        return $svgTemplate;

    }


    public function calculateCircleLineEndPoint($x1, $y1, $x2, $y2, $round)
    {
        $distance = floatval(sqrt(($x2 - $x1) * ($x2 - $x1) + ($y2 - $y1) * ($y2 - $y1)));
        $tan = floatval($y2 - $y1) / floatval($x2 - $x1);
        $_x = floatval($round) / floatval($distance) * floatval($x2 - $x1);
        $_y = $_x * $tan;


        return [$x2 - $_x, $y2 - $_y];
    }

    public function calculateCircleLineStartPoint($x1, $y1, $x2, $y2, $round)
    {
        $distance = floatval(sqrt(($x2 - $x1) * ($x2 - $x1) + ($y2 - $y1) * ($y2 - $y1)));
        $tan = floatval($y2 - $y1) / floatval($x2 - $x1);
        $_x = floatval($round) / floatval($distance) * floatval($x2 - $x1);
        $_y = $_x * $tan;


        return [$x1 + $_x, $y1 + $_y];
    }

}