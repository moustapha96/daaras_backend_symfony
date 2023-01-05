<?php

namespace App\Controller;

use App\Entity\CodeResetPassword;
use App\Entity\UserMobile;

use App\Repository\CodeResetPasswordRepository;
use App\Repository\UserMobileRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\VarDumper\Cloner\Data;

use Symfony\Component\Form\Extension\Core\Type\DateTime;

class MyCodeRessetPasswordController extends AbstractController
{

    /**
     * @Route("/api/verfiyCode", name="app_verify_code",methods={"POST"})
     */
    public function verifiyCode(Request $request, UserMobileRepository $repo, CodeResetPasswordRepository $codeResetPasswordRepository): ?Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $user_id = $data['user_id'];
            $code = $data['code'];
            $user = $repo->findOneBy(['id' => $user_id]);
            $codeRp = $codeResetPasswordRepository->findOneBy(['code' => $code]);
            // $dateC = new DateTime ($codeRp->getDateCreateAt().getDate());
            if ($codeRp ==  null) {
                throw  new  NotFoundHttpException(" code  $code non trouvé");
            } else {
                if ($codeRp->getEnable() == false) {
                    throw  new  NotFoundHttpException(" code  $code deja utiliser");
                } else if ($codeRp->getEnable() == false && $codeRp->getDateCreateAt() < $codeRp->getDateExpirate()) {
                    throw  new  NotFoundHttpException("$code expiré !!");
                }
                // return new JsonResponse($codeRp->asArray(), 200, ["Content-Type" => "application/json"]);
            }

            // return new JsonResponse($user->asArray(), 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 500);
        }
    }
}
