<?php
/**
 * @package     jelix
 * @subpackage  jtpl_plugin
 * @author      Laurent Jouanneau
 * @copyright   2007 Laurent Jouanneau
 * @contributor Christian Tritten (christian.tritten@laposte.net)
 * @contributor Michel MA (michgeek)
 * @copyright   2007 Christian Tritten, 2012 Michel MA
 * @link        http://www.jelix.org
 * @licence     GNU Lesser General Public Licence see LICENCE file or
 * http://www.gnu.org/licenses/lgpl.html
 */

/**
 * Alternative pager to {pagelinks}
 *
 * Displays page links with incremental page instead of offset
 * @example ?page=2, ?page=3, etc.
 * @param jTpl $tpl template engine
 * @param string $action selector of the action
 * @param array $actionParams parameters for the action
 * @param integer $itemsTotal number of items
 * @param integer $currentPage default = 1 the current Page
 * @param integer $pageSize  items number in a page
 * @param string $paramName name of the parameter in the actionParams which will
 * content a page offset
 * @param array $displayProperties properties for the links display
 * @formatter:off
 **/
function jtpl_function_html_incrementalpagelinks($tpl, $action, $actionParams, $itemsTotal, $currentPage, $pageSize = 15, 
                                      $paramName = 'page', $displayProperties = array())
{
    // @formatter:on
    $currentPage = intval($currentPage);
    if ($currentPage <= 0)
        $currentPage = 1;

    $itemsTotal = intval($itemsTotal);

    $pageSize = intval($pageSize);
    if ($pageSize < 1)
        $pageSize = 1;

    // If there are at least two pages of results
    if ($itemsTotal > $pageSize) {
        $jUrlEngine = jUrl::getEngine();

        $urlaction = jUrl::get($action, $actionParams, jUrl::JURLACTION);

        // @formatter:off
        $defaultDisplayProperties = array('start-label' => '|&lt;',
                                          'prev-label'  => '&lt;',
                                          'next-label'  => '&gt;',
                                          'end-label'   => '&gt;|',
                                          'area-size'   => 0);
        // @formatter:on

        if (is_array($displayProperties) && count($displayProperties) > 0) {
            $displayProperties = array_merge($defaultDisplayProperties, $displayProperties);
        } else {
            $displayProperties = $defaultDisplayProperties;
        }

        $pages = array();

        $numpage = 1;

        $prevBound = 0;

        $nextBound = 0;

        $totalPage = ceil($itemsTotal / $pageSize);

        // Generates list of page offsets
        for ($curidx = 1; $curidx <= $totalPage; $curidx++) {
            if ($currentPage == $curidx) {
                $pages[$numpage] = '<li class="pagelinks-current">' . $numpage . '</li>';
                $prevBound = $curidx - 1;
                $nextBound = $curidx + 1;
                $currentPage = $numpage;
            } else {
                if ($numpage > 1) {
                    $urlaction->params[$paramName] = $curidx;
                } else {
                    unset($urlaction->params[$paramName]);
                }
                $url = $jUrlEngine->create($urlaction);
                $pages[$numpage] = '<li><a href="' . $url->toString(true) . '">' . $numpage . '</a></li>';
            }
            $numpage++;
        }

        // Calculate start page url
        unset($urlaction->params[$paramName]);
        $urlStartPage = $jUrlEngine->create($urlaction);

        // Calculate previous page url
        if ($prevBound > 1) {
            $urlaction->params[$paramName] = $prevBound;
        } else {
            unset($urlaction->params[$paramName]);
        }
        $urlPrevPage = $jUrlEngine->create($urlaction);

        // Calculate next page url
        $urlaction->params[$paramName] = $nextBound;
        $urlNextPage = $jUrlEngine->create($urlaction);

        // Calculate end page url
        $urlaction->params[$paramName] = $totalPage;
        $urlEndPage = $jUrlEngine->create($urlaction);

        // Links display
        echo '<ul class="pagelinks">';

        // Start link
        if (!empty($displayProperties['start-label'])) {
            echo '<li class="pagelinks-start';
            if ($prevBound >= 1) {
                echo '"><a href="', $urlStartPage->toString(true), '">', $displayProperties['start-label'], '</a>';
            } else {
                echo ' pagelinks-disabled">', $displayProperties['start-label'];
            }
            echo '</li>', "\n";
        }

        // Previous link
        if (!empty($displayProperties['prev-label'])) {
            echo '<li class="pagelinks-prev';
            if ($prevBound >= 1) {
                echo '"><a href="', $urlPrevPage->toString(true), '">', $displayProperties['prev-label'], '</a>';
            } else {
                echo ' pagelinks-disabled">', $displayProperties['prev-label'];
            }
            echo '</li>', "\n";
        }

        // Pages links
        foreach ($pages as $key => $page) {
            if ($displayProperties['area-size'] == 0 || ($currentPage - $displayProperties['area-size'] <= $key) && ($currentPage + $displayProperties['area-size'] >= $key)) {
                echo $page, "\n";
            }
        }

        // Next link
        if (!empty($displayProperties['next-label'])) {
            echo '<li class="pagelinks-next';
            if ($nextBound <= $totalPage) {
                echo '"><a href="', $urlNextPage->toString(true), '">', $displayProperties['next-label'], '</a>';
            } else {
                echo ' pagelinks-disabled">', $displayProperties['next-label'];
            }
            echo '</li>', "\n";
        }

        // End link
        if (!empty($displayProperties['end-label'])) {
            echo '<li class="pagelinks-end';
            if ($nextBound <= $totalPage) {
                echo '"><a href="', $urlEndPage->toString(true), '">', $displayProperties['end-label'], '</a>';
            } else {
                echo ' pagelinks-disabled">', $displayProperties['end-label'];
            }
            echo '</li>', "\n";
        }

        echo '</ul>';
    } else {
        echo '<ul class="pagelinks"><li class="pagelinks-current">1</li></ul>';
    }
}
