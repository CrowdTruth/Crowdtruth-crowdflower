<?php
namespace CrowdTruth\Crowdflower;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use CrowdTruth\Crowdflower\Cfapi\CFExceptions;
use \Entities\Workerunit as Workerunit;
use \CrowdAgent as CrowdAgent;
use \MongoDB\Agent;
use \Entities\Job as Job;
use \Log;
use \QuestionTemplate;
use \MongoDate;
use \Queue;
use \Entity as Entity;
use \Activity as Activity;

class RetrieveJobs extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'CF:retrievejobs';
	//protected $name = 'cf:retrievejobs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retrieve workerunits from CrowdFlower and update job status.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$newJudgmentsCount = 0;

		try {
			if($this->option('jobid')){
				die('not yet implemented');
				// We could check for each workerunit and add it if somehow it didn't get added earlier.
				// For this, we should add ifexists checks in the storeJudgment method.
				$cfjobid = $this->option('jobid');
				$cf = new CrowdTruth\Crowdflower\Cfapi\Job(Config::get('crowdflower::apikey'));
				$judgments = ''; //todo
			}

			if($this->option('judgments')) {
				$judgments = unserialize($this->option('judgments'));
				$cfjobid = $judgments[0]['job_id']; // We assume that all judgments have the same jobic
			}

			$judgment = $judgments[0];
			$agentId = "crowdagent/CF/{$judgment['worker_id']}";
			$ourjobid = $this->getJob($cfjobid)->_id;


			// TODO: check if exists. How?
			// For now this hacks helps: else a new activity would be created even if this
			// command was called as the job is finished. It doesn't work against manual calling the command though.
			if($this->option('judgments')) {
				try {
					$activity = new Activity;
					$activity->label = "Units are annotated on crowdsourcing platform.";
					$activity->crowdAgent_id = $agentId;
					$activity->used = $ourjobid;
					$activity->softwareAgent_id = 'CF';
					$activity->save();
				} catch (Exception $e) {
					if($activity) $activity->forceDelete();
					throw new Exception('Error saving activity for workerunit.');
				}
			}

			// Store judgments.
			foreach($judgments as $judgment)
				$this->storeJudgment($judgment, $ourjobid, $activity->_id, $agentId);

			// Create or update Agent
			if(!$agent = CrowdAgent::id($agentId)->first()){
				$agent = new CrowdAgent;
				$agent->_id= $agentId;
				$agent->softwareAgent_id= 'CF';
				$agent->platformAgentId = (string) $judgment['worker_id'];
				$agent->country = $judgment['country'];
				$agent->region = $judgment['region'];
				$agent->city = $judgment['city'];
			}

			$agent->cfWorkerTrust = $judgment['worker_trust'];

			Queue::push('Queues\UpdateCrowdAgent', array('crowdagent' => serialize($agent)));

			$job = $this->getJob($cfjobid);
			Queue::push('Queues\UpdateJob', array('job' => serialize($job)));

			//Log::debug("Saved new workerunits to {$job->_id} to DB.");
		} catch (CFExceptions $e){
			Log::warning($e->getMessage());
			throw $e;
		} catch (Exception $e) {
			Log::warning($e->getMessage());
			throw $e;
		}
		// If we throw an error, crowdflower will recieve HTTP 500 (internal server error) from us and try again.
	}

	/**
	* Retrieve Job from database.
	* @return Entity (documentType:job)
	* @throws CFExceptions when no job is found.
	*/
	private function getJob($jobid){
		if(!$job = Job::where('softwareAgent_id', 'CF')
						->where('platformJobId', intval($jobid)) /* Mongo queries are strictly typed! We saved it as int in Job->store */
						->first())
		{
			$job = Job::where('softwareAgent_id', 'CF')
						->where('platformJobId', (string) $jobid) /* Try this to be sure. */
						->first();
		}

		// Still no job found, this job is probably not made in our platform (or something went wrong earlier)
		if(!$job) {
			Log::warning("Callback from CF to our server for Job $jobid, which is not in our DB.");
			throw new CFExceptions("CFJob not in local database; retrieving it would break provenance.");
			// TODO discuss: we could also decide to create a new job with all the info we can get.
		}

		return $job;
	}


	/**
	* @return true if created, false if exists
	*/
	private function storeJudgment($judgment, $ourjobid, $activityId, $agentId)
	{

		// If exists return false.
		if(Workerunit::where('softwareAgent_id', 'CF')
			->where('platformWorkerunitId', $judgment['id'])
			->first())
			return false;

		try {

			$workerunit = new Workerunit;
			$workerunit->activity_id = $activityId;			
			$workerunit->unit_id = $judgment['unit_data']['uid'];			
			$workerunit->acceptTime = new MongoDate(strtotime($judgment['started_at']));
			$workerunit->cfChannel = $judgment['external_type'];
			$workerunit->cfTrust = $judgment['trust'];
			$workerunit->content = $judgment['data'];
			$workerunit->crowdAgent_id = $agentId;
			$workerunit->job_id = $ourjobid;
			$workerunit->platformWorkerunitId = $judgment['id'];
			$workerunit->submitTime = new MongoDate(strtotime($judgment['created_at']));
			$workerunit->documentType = $settings['documentType'];
			$workerunit->templateType = $settings['templateType'];
			$workerunit->project = $settings['project'];
			$workerunit->softwareAgent_id = 'CF';

			Queue::push('Queues\SaveWorkerunit', array('workerunit' => serialize($workerunit)));

			return $workerunit;

		} catch (Exception $e) {
			Log::warning("E:{$e->getMessage()} while saving workerunit with CF id {$judgment['id']} to DB.");
			if($workerunit) $workerunit->forceDelete();
			// TODO: more?
		}
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('judgments', null, InputOption::VALUE_OPTIONAL, 'A full serialized collection of judgements from the CF API. Will insert into DB.', null),
			array('jobid', null, InputOption::VALUE_OPTIONAL, 'CF Job ID.', null)
		);
	}
}
