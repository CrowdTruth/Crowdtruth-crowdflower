<?php

namespace CrowdTruth\Crowdflower\Cfapi;

require_once 'CFAddFunctions.php';
class CFWebhook {
	
	/**
	 * The -signal- parameter describes the type of event that has occurred.
	 *
	 * The -payload- parameter will contain a JSON representation of the associated object.
	 * 
	 * @return type of signal, job id
	 * @link http://crowdflower.com/docs-api#webhooks
	 */
	public function getSignal() {
		$signal = \Input::get ( 'signal' ); // Can't use post because of Laravel
		$payload = \Input::get ( 'payload' );
		
		if ($signal == "new_judgments") {
			$retValue = objectToArray ( json_decode ( $payload ) );
			// sleep(1);
			$this->handleNewJudgments ( $retValue );
		}
		if ($signal == "job_complete") {
			$retValue = objectToArray ( json_decode ( $payload ) );
			$job_id = $retValue ["id"];
			$this->handleJobComplete ( $job_id );
		}
		die ( "$signal - ok" );
	}
	
	/**
	 * TODO: update the number of judgments for a job and the completion percentage
	 * 
	 * @link http://crowdflower.com/docs-api#webhooks
	 */
	private function handleNewJudgments($judgments) {
		// foreach($judgments as $judgment)
		\Artisan::call ( 'CF:retrievejobs', array (
				'--judgments' => serialize ( $judgments ) 
		) );
	}
	
	/**
	 * TODO: update the running time for a job and based on the type of job apply the sentence and worker metrics
	 * 
	 * @link http://crowdflower.com/docs-api#webhooks
	 */
	private function handleJobComplete($job_id) {
		return;
	}
	
	/**
	 * For testing only.
	 * Example returndata for new judgments.
	 */
	public function test($jobid) {
		// Test data:
		$signal = 'new_judgments';
		$payload = '[{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:16+00:00","data":{"direction":"whooping cough causes b. pertussis"},"unit_id":422728037,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575432,"external_type":"cf_internal","unit_data":{"relation_noprefix":"contraindicated","terms_first_text":"Healing","terms_second_endindex":"37","sentence_text":"title =The Organon of the Healing Art.","terms_first_startindex":"26","sentence_startindex":"53","terms_second_text":"Art","uid":"entity/text/medical/relex-structured-sentence/287","terms_second_startindex":"34","relation_original":"relex-contraindicated","terms_first_endindex":"33","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:02+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:16+00:00","data":{"direction":"atherosclerosis contraindicated methionine"},"unit_id":422728038,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575433,"external_type":"cf_internal","unit_data":{"relation_noprefix":"contraindicated","terms_first_text":"atherosclerosis","terms_second_endindex":"33","sentence_text":"Improper conversion of methionine can lead to atherosclerosis.","terms_first_startindex":"46","sentence_startindex":"68","terms_second_text":"methionine","uid":"entity/text/medical/relex-structured-sentence/291","terms_second_startindex":"23","relation_original":"relex-contraindicated","terms_first_endindex":"61","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:02+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:16+00:00","data":{"direction":"Disorders location body"},"unit_id":422728045,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575434,"external_type":"cf_internal","unit_data":{"relation_noprefix":"location","terms_first_text":"body","terms_second_endindex":"9","sentence_text":"Disorders of iris and ciliary body.","terms_first_startindex":"30","sentence_startindex":"41","terms_second_text":"Disorders","uid":"entity/text/medical/relex-structured-sentence/701","terms_second_startindex":"0","relation_original":"relex-location","terms_first_endindex":"34","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:02+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:16+00:00","data":{"direction":"Tubal ligation contraindicated pregnancy"},"unit_id":422728033,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575435,"external_type":"cf_internal","unit_data":{"relation_noprefix":"contraindicated","terms_first_text":"pregnancy","terms_second_endindex":"14","sentence_text":"Tubal ligation can predispose to ectopic pregnancy.","terms_first_startindex":"41","sentence_startindex":"65","terms_second_text":"Tubal ligation","uid":"entity/text/medical/relex-structured-sentence/226","terms_second_startindex":"0","relation_original":"relex-contraindicated","terms_first_endindex":"50","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:02+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:16+00:00","data":{"direction":"no_relation"},"unit_id":422728041,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575436,"external_type":"cf_internal","unit_data":{"relation_noprefix":"contraindicated","terms_first_text":"nitrate","terms_second_endindex":"47","sentence_text":"OtherCations = Sodium nitrate Potassium nitrate Hydroxylammonium nitrate.","terms_first_startindex":"65","sentence_startindex":"57","terms_second_text":"nitrate","uid":"entity/text/medical/relex-structured-sentence/368","terms_second_startindex":"40","relation_original":"relex-contraindicated","terms_first_endindex":"72","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:02+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:16+00:00","data":{"direction":"sex contraindicated hormones"},"unit_id":422728032,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575437,"external_type":"cf_internal","unit_data":{"relation_noprefix":"contraindicated","terms_first_text":"sex","terms_second_endindex":"28","sentence_text":"GnRH - LH/FSH - sex hormones.","terms_first_startindex":"16","sentence_startindex":"53","terms_second_text":"hormones","uid":"entity/text/medical/relex-structured-sentence/204","terms_second_startindex":"20","relation_original":"relex-contraindicated","terms_first_endindex":"19","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:02+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:16+00:00","data":{"direction":"thyroid location Inflammation"},"unit_id":422728049,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575438,"external_type":"cf_internal","unit_data":{"relation_noprefix":"location","terms_first_text":"thyroid","terms_second_endindex":"12","sentence_text":"Inflammation of the thyroid is called thyroiditis.","terms_first_startindex":"20","sentence_startindex":"49","terms_second_text":"Inflammation","uid":"entity/text/medical/relex-structured-sentence/794","terms_second_startindex":"0","relation_original":"relex-location","terms_first_endindex":"27","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:02+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:29+00:00","data":{"direction":"hormone contraindicated condition"},"unit_id":422728036,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575453,"external_type":"cf_internal","unit_data":{"relation_noprefix":"contraindicated","terms_first_text":"condition","terms_second_endindex":"14","sentence_text":"Growth hormone deficiency, a medical condition.","terms_first_startindex":"37","sentence_startindex":"58","terms_second_text":"hormone","uid":"entity/text/medical/relex-structured-sentence/275","terms_second_startindex":"7","relation_original":"relex-contraindicated","terms_first_endindex":"46","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:17+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:29+00:00","data":{"direction":"Conditions diagnose test"},"unit_id":422728043,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575454,"external_type":"cf_internal","unit_data":{"relation_noprefix":"diagnose","terms_first_text":"Conditions","terms_second_endindex":"34","sentence_text":"Conditions diagnosed by stool test.","terms_first_startindex":"0","sentence_startindex":"43","terms_second_text":"test","uid":"entity/text/medical/relex-structured-sentence/446","terms_second_startindex":"30","relation_original":"relex-diagnose","terms_first_endindex":"10","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:17+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:29+00:00","data":{"direction":"fluorescence contraindicated berberine"},"unit_id":422728034,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575456,"external_type":"cf_internal","unit_data":{"relation_noprefix":"contraindicated","terms_first_text":"fluorescence","terms_second_endindex":"34","sentence_text":"Under ultraviolet light, berberine shows a strong yellow fluorescence.","terms_first_startindex":"57","sentence_startindex":"64","terms_second_text":"berberine","uid":"entity/text/medical/relex-structured-sentence/230","terms_second_startindex":"25","relation_original":"relex-contraindicated","terms_first_endindex":"69","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:17+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:29+00:00","data":{"direction":"Renewal contraindicated movement"},"unit_id":422728035,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575457,"external_type":"cf_internal","unit_data":{"relation_noprefix":"contraindicated","terms_first_text":"movement","terms_second_endindex":"7","sentence_text":"Renewal (documentary), a 2008 documentary on the religious environmental movement.","terms_first_startindex":"73","sentence_startindex":"56","terms_second_text":"Renewal","uid":"entity/text/medical/relex-structured-sentence/247","terms_second_startindex":"0","relation_original":"relex-contraindicated","terms_first_endindex":"81","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:17+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:29+00:00","data":{"direction":"flow cause priapism"},"unit_id":422728029,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575460,"external_type":"cf_internal","unit_data":{"relation_noprefix":"cause","terms_first_text":"priapism","terms_second_endindex":"41","sentence_text":"There are two types of priapism: low-flow and high-flow.","terms_first_startindex":"23","sentence_startindex":"40","terms_second_text":"flow","uid":"entity/text/medical/relex-structured-sentence/93","terms_second_startindex":"37","relation_original":"relex-cause","terms_first_endindex":"31","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:17+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:29+00:00","data":{"direction":"pharyngitis location throat"},"unit_id":422728046,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575461,"external_type":"cf_internal","unit_data":{"relation_noprefix":"location","terms_first_text":"throat","terms_second_endindex":"11","sentence_text":"pharyngitis (sore throat).","terms_first_startindex":"18","sentence_startindex":"47","terms_second_text":"pharyngitis","uid":"entity/text/medical/relex-structured-sentence/708","terms_second_startindex":"0","relation_original":"relex-location","terms_first_endindex":"24","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:17+00:00","job_id":394586,"golden":false,"rejected":null},{"city":"","webhook_sent_at":null,"region":"","created_at":"2014-03-03T09:17:29+00:00","data":{"direction":"penicillin prevent Streptococcus"},"unit_id":422728050,"unit_state":"judgable","country":"EU","worker_trust":0.916666666666667,"judgment":1,"tainted":false,"trust":1.0,"id":1213575462,"external_type":"cf_internal","unit_data":{"relation_noprefix":"prevent","terms_first_text":"Streptococcus","terms_second_endindex":"31","sentence_text":"It is active against penicillin-resistant strains of Streptococcus pneumoniae.","terms_first_startindex":"53","sentence_startindex":"53","terms_second_text":"penicillin","uid":"entity/text/medical/relex-structured-sentence/911","terms_second_startindex":"21","relation_original":"relex-prevent","terms_first_endindex":"66","_golden":"false"},"reviewed":null,"worker_id":19935135,"missed":null,"started_at":"2014-03-03T09:17:17+00:00","job_id":394586,"golden":false,"rejected":null}]';
		$payload = str_replace ( '394586', $jobid, $payload );
		
		$p2 = json_decode ( $payload );
		foreach ( $p2 as $p2part ) {
			$retValue = objectToArray ( $p2part );
			$this->handleNewJudgments ( array (
					$retValue 
			) );
		}
	}
}

