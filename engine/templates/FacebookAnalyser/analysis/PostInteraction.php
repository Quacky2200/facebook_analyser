<?php
require(__DIR__ . '/AnalysisElement.php');
	class PostInteraction extends AnalysisElement{

		const LIKE_SCORE = 1;
		const TAGGED_SCORE = 10;
		const POSITIVE_COMMENT_SCORE = 10;
		const NEGATIVE_COMMENT_SCORE = -5;
		const NEUTRAL_COMMENT_SCORE = 5;

		private $POSITIVE_WORD_LIST;
		private $NEGATIVE_WORD_LIST;

		private $interaction = array();

		public function __construct(){
			$this->POSITIVE_WORD_LIST = file(__DIR__ . '/dictionary/positive words EN-GB.txt');
			$this->NEGATIVE_WORD_LIST = file(__DIR__ . '/dictionary/negative words EN-GB.txt');
		}

		public function analyse($data){
			foreach ($data['posts'] as $post){
				$this->addLikes($post);
				$this->addComments($post);
				$this->addWithTags($post);
				$this->addMessageTags($post);
			}
			//Remove the user from the results if they are included.
			if (array_key_exists(User::instance()->id, $this->interaction)) unset($this->interaction[User::instance()->id]);
			return $this->interaction;
		}

		private function attachUser($id, $name){
			if (!array_key_exists($id, $this->interaction)){
				$this->interaction[$id] = array(
					'name' => $name,
					'likes' => 0,
					'comments' => 0,
					'tags' => 0
				);
			}
		}

		private function addLikes($post){
			if(!array_key_exists('likes', $post)) return;
			foreach ($post['likes'] as $like){
				$this->attachUser($like['id'], $like['name']);
				$this->interaction[$like['id']]['likes'] += self::LIKE_SCORE;
			}
		}

		private function addComments($post){
			if(!array_key_exists('comments', $post)) return;
			foreach ($post['comments'] as $comment){
				$this->attachUser($comment['from']['id'], $comment['from']['name']);
				$this->interaction[$comment['from']['id']]['comments'] += $this->getCommentSentiment($comment['message']);
			}
		}

		private function addWithTags($post){
			if(!array_key_exists('with_tags', $post)) return;
			foreach ($post['with_tags'] as $tag){
				$this->attachUser($tag['id'], $tag['name']);
				$this->interaction[$tag['id']]['tags'] += self::TAGGED_SCORE;
			}
		}

		private function addMessageTags($post){
			if(!array_key_exists('message_tags', $post)) return;
			foreach ($post['message_tags'] as $tag){
				$this->attachUser($tag['id'], $tag['name']);
				$this->interaction[$tag['id']]['tags'] += self::TAGGED_SCORE;
			}
		}
		private function arrayWordCountInText($arr, $str){
			//How many words from the array can be found in the text?
			$history = "";
			$count = 0;
			foreach ($arr as $word){
				//If we find a word in the array that hasn't ALREADY been found
				//E.g. 'Hello World' was found and we're looking at 'Hello' but 'Hello World' was already in the spectrum first.
				if (stripos($word, $str) !== false && stripos($word, $history) !== false){
					$history += " $word";
					$count += 1;
				}
			}
			return $count;

		}
		private function getCommentSentiment($comment){
			$negativeWordCount = $this->arrayWordCountInText($this->NEGATIVE_WORD_LIST, $comment);
			$positiveWordCount = $this->arrayWordCountInText($this->POSITIVE_WORD_LIST, $comment);
			if ($negativeWordCount > $positiveWordCount){
				return self::NEGATIVE_COMMENT_SCORE;
			} else if($positiveWordCount > $negativeWordCount){
				return self::POSITIVE_COMMENT_SCORE;
			} else {
				return self::NEUTRAL_COMMENT_SCORE;
			}
		}


		private function getCommentContext($comment) {

		}
	}
?>