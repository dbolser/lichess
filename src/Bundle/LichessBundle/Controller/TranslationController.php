<?php

namespace Bundle\LichessBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\ChoiceField;
use Symfony\Component\Form\TextareaField;
use Symfony\Component\Form\TextField;
use Bundle\LichessBundle\Entities\Translation;
use Symfony\Component\Finder\Finder;

class TranslationController extends Controller
{

    public function indexAction($locale)
    {
        $locales = include(__DIR__.'/../I18N/locales.php');
        unset($locales['en']);
        ksort($locales);
        array_unshift($locales, 'Choose a language');
        $translation = new Translation();
        if(' ' === $locale) {
            $locale = null;
        }
        else {
            $translation->setCode($locale);
            $translation->setName($locales[$locale]);
        }
        try {
            $translation->setMessages($this->container->getLichessTranslatorService()->getMessages($locale));
        }
        catch(\InvalidArgumentException $e) {
            $translation->setEmptyMessages($this->container->getLichessTranslatorService()->getMessages('fr'));
        }
        $form = new Form('translation', $translation, $this->container->getValidatorService());
        $form->add(new ChoiceField('code', array('choices' => $locales)));
        $form->add(new TextareaField('yamlMessages'));
        $form->add(new TextField('author'));
        $form->add(new TextField('comment'));

        if ($this['request']->getMethod() == 'POST')
        {
            try {
                $form->bind($this['request']->request->get('translation'));
                $fileName = sprintf("%s_%s-%d", $translation->getCode(), date("Y-m-d_h-i-s"), time());
                $fileContent = sprintf("#%s\n#%s\n#%s\n%s\n", $translation->getName(), $translation->comment, $translation->author, $translation->getYamlMessages());
                $file = sprintf('%s/translation/%s', $this->container->getParameter('kernel.root_dir'), $fileName);
                if(!@file_put_contents($file, $fileContent)) {
                    throw new \Exception('Submit failed due to an internal error. please send a mail containing your translation to thibault.duplessis@gmail.com');
                }
                $message = 'Your translation has been submitted, thanks!';
            }
            catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return $this->render('LichessBundle:Translation:index', array(
            'form' => $form,
            'locale' => $locale,
            'message' => isset($message) ? $message : null,
            'error' => isset($error) ? $error : null,
        ));
    }

    public function listAction()
    {
        $finder = new Finder;
        $files = $finder->files()->in(sprintf('%s/translation', $this->container->getParameter('kernel.root_dir')));

        $days = array();
        foreach($files as $file) {
            if(preg_match('/^([\w]+)_([\d-]+)_(\d{2}-){3}(\d+)$/', basename($file), $matches))
            {
                list($name, $locale, $date, $time, $ts) = $matches;
                if(!isset($days[$date])) {
                    $days[$date] = array();
                }
                $dateObject = new \DateTime();
                $dateObject->setTimestamp($ts);
                $days[$date][$ts] = array('file' => $file, 'locale' => $locale, 'date' => $dateObject);
            }
        }

        krsort($days);
        foreach($days as $date => &$times) {
            krsort($times);
        }

        return $this->render('LichessBundle:Translation:list', array('days' => $days));
    }
}
