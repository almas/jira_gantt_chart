<?php
/**
 * Created by PhpStorm.
 * User: apremiera
 * Date: 07.08.17
 * Time: 19:36
 */

namespace GanttChartBundle\Repository;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JiraRepository
{

    /**
     * Идентификаторы статусов задач в Jira
     */
    const NEW_TASK_ID = 1;
    const PLANNING_TASK_ID = 10014;

    const QUERY_LINIT = 30;

    private $jiraConfig;

    /**
     * JiraRepository constructor.
     */
    public function __construct(ParameterBagInterface $params)
    {

        // TODO Конфигурацию необходимо передавать извне
        $this->jiraConfig = new ArrayConfiguration(
            array(
                'jiraHost' => $params->get('jira_host'), //without close slash
                'jiraUser' => $params->get('jira_user'), //set user auth name
                'jiraPassword' => $params->get('jira_api_key') //set user passwd or apikey
            )
        );
    }


    /**
     * @param $params
     *
     * @return \JiraRestApi\Issue\Issue[]
     */
    public function jiraGetByJqlSearch($params)
    {

        try {
            $issueService = new IssueService($this->jiraConfig);

            $result = $issueService->search($params['jql'], 0, $params['limit']);

        } catch (JiraException $e) {
            // @TODO some
            print_r($e->getMessage());
            exit;
        }

        return $result->getIssues();
    }


    /**
     * Получает issue по идентификатору
     * @param $issueId string
     * @return \stdClass
     */
    public function jiraGetIssue($issueId, $queryParam = [])
    {
        try {
            $issueService = new IssueService($this->jiraConfig);

            /*$queryParam = [
                'fields' => [  // default: '*all'
                        'summary',
                        'comment',
                     ],
                     'expand' => [
                        'renderedFields',
                        'names',
                        'schema',
                        'transitions',
                        'operations',
                        'editmeta',
                        'changelog',
                     ]
            ];*/

            $issue = $issueService->get($issueId, $queryParam);

            $result = $issue->fields;

        } catch (JiraException $e) {
            $this->assertTrue(false, 'jiraGetIssue' . $e->getMessage());
        }

        return $result;
    }

    /**
     * @return ArrayConfiguration
     */
    public function getJiraConfig()
    {
        return $this->jiraConfig;
    }
}