<?php

/**
 * AVCC
 * 
 * @category AVCC
 * @package  Application
 * @author   Nouman Tayyab <nouman@weareavp.com>
 * @author   Rimsha Khalid <rimsha@weareavp.com>
 * @license  AGPLv3 http://www.gnu.org/licenses/agpl-3.0.txt
 * @copyright Audio Visual Preservation Solutions, Inc
 * @link     http://avcc.weareavp.com
 */

namespace Application\Bundle\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\Bundle\FrontBundle\Entity\Users;
use Application\Bundle\FrontBundle\Form\UsersType;
use Application\Bundle\FrontBundle\Entity\Records;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Application\Bundle\FrontBundle\Helper\DefaultFields as DefaultFields;
use Application\Bundle\FrontBundle\Entity\UserSettings as UserSettings;
use Application\Bundle\FrontBundle\SphinxSearch\SphinxSearch;
use Application\Bundle\FrontBundle\Helper\EmailHelper;
use Application\Bundle\FrontBundle\Controller\MyController;

/**
 * Users controller.
 *
 * @Route("/users")
 */
class UsersController extends MyController {

    /**
     * Lists all Users entities.
     *
     * @param integer $orgId
     * @param integer $roleId
     * 
     * @Route("/", name="users")
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function indexAction($orgId = null, $roleId = null) {
        @set_time_limit(0);
        @ini_set("memory_limit", "1000M"); # 1GB
        @ini_set("max_execution_time", 0); # unlimited
        $session = $this->getRequest()->getSession();
        if (($session->has('termsStatus') && $session->get('termsStatus') == 0) || ($session->has('limitExceed') && $session->get('limitExceed') == 0)) {
            return $this->redirect($this->generateUrl('dashboard'));
        }
        $organizations = array();
        $em = $this->getDoctrine()->getManager();
        $org = array();
        $currentUserId = $this->getUser()->getId();
        if (true === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN') && $orgId) {
            $entities = $em->getRepository('ApplicationFrontBundle:Users')->getUsersWithoutCurentLoggedIn($currentUserId, $orgId);
            $organizations = $em->getRepository('ApplicationFrontBundle:Users')->getUsersWithoutCurentLoggedIn($currentUserId);
        } else if (true === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $entities = $organizations = $em->getRepository('ApplicationFrontBundle:Users')->getUsersWithoutCurentLoggedIn($currentUserId);
        } else {
            $entities = $em->getRepository('ApplicationFrontBundle:Users')->getUsersWithoutCurentLoggedIn($currentUserId, $this->getUser()->getOrganizations()->getId());
        }
        $all = array();
        $currentUserRole = $this->getUser()->getRoles();
        if ($currentUserRole[0] == 'ROLE_MANAGER' || $currentUserRole[0] == 'ROLE_ADMIN') {
            foreach ($entities as $key => $entity) {
                $role = $entity->getRoles();
                if ($role[0] != 'ROLE_SUPER_ADMIN') {
                    $all[$key] = $entity;
                }
            }
        } else if ($roleId) {
            foreach ($entities as $key => $entity) {
                $role = $entity->getRoles();
                if ($roleId == 1) {
                    if ($role[0] == 'ROLE_SUPER_ADMIN') {
                        $all[$key] = $entity;
                    }
                } else if ($roleId == 2) {
                    if ($role[0] == 'ROLE_ADMIN') {
                        $all[$key] = $entity;
                    }
                } else if ($roleId == 3) {
                    if ($role[0] == 'ROLE_MANAGER') {
                        $all[$key] = $entity;
                    }
                } else if ($roleId == 4) {
                    if ($role[0] == 'ROLE_CATALOGER') {
                        $all[$key] = $entity;
                    }
                } else if ($roleId == 5) {
                    if ($role[0] == 'ROLE_USER') {
                        $all[$key] = $entity;
                    }
                }
            }
        } else {
            $all = $entities;
        }

        foreach ($organizations as $entity) {
            if ($entity->getOrganizations()) {
                $org[$entity->getOrganizations()->getId()] = $entity->getOrganizations()->getName();
            }
        }

        if ($roleId == null) {
            $roleId = 0;
        }
        if ($orgId == null) {
            $orgId = 0;
        }
        return array(
            'entities' => $all,
            'role' => $currentUserRole[0],
            'organization' => $org,
            'org_id' => $orgId,
            'role_id' => $roleId
        );
    }

    /**
     * Creates a new Users entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @Route("/", name="users_create")
     * @Method("POST")
     * @Template("ApplicationFrontBundle:Users:new.html.twig")
     *
     * @return array/redirect to list page
     */
    public function createAction(Request $request) {

        $entity = new Users();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $record = $request->request->get('application_bundle_frontbundle_users');
        $password = $record['plainPassword']['second'];
        if ($form->isValid()) {
            $entity->setEnabled(true);
            $entity->setUsersCreated($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $user_entity = new UserSettings();
            $user_entity->setUser($entity);
            $user_entity->setCreatedOnValue(date('Y-m-d h:i:s'));
            $em->persist($user_entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'User added succesfully.');
            // $rendered = 'username: ' . $entity->getUsername(); 

            $parameters = array('user' => $entity, 'url' => $this->container->getParameter('baseUrl'), 'admin' => $this->getUser()->getName(), 'admin_email' => $this->getUser()->getEmail(), 'password' => $password);
            $rendered = $this->container->get('templating')->render('ApplicationFrontBundle:Users:email.html.php', $parameters);
            $email = new EmailHelper($this->container);
            $subject = 'Confirmation Email';
            $email->sendEmail($rendered, $subject, $this->container->getParameter('from_email'), $entity->getEmail());
            return $this->redirect($this->generateUrl('users'));
        }
        $organizationId = '';
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $organizationId = $this->getUser()->getOrganizations()->getId();
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'organizationId' => $organizationId
        );
    }

    /**
     * Creates a form to create a Users entity.
     *
     * @param \Application\Bundle\FrontBundle\Entity\Users $entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Users $entity) {
        $formOptions = $this->getRoleHierarchy();
        $formOptions['currentUser'] = $this->getUser();
        $form = $this->createForm(new UsersType($formOptions), $entity, array(
            'action' => $this->generateUrl('users_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Users entity.
     *
     * @Route("/new", name="users_new")
     * @Method("GET")
     * @Template()
     *
     * @return array
     */
    public function newAction() {

        $session = $this->getRequest()->getSession();
        if (($session->has('termsStatus') && $session->get('termsStatus') == 0) || ($session->has('limitExceed') && $session->get('limitExceed') == 0)) {
            return $this->redirect($this->generateUrl('dashboard'));
        }
        $entity = new Users();
        $form = $this->createCreateForm($entity);
        $organizationId = '';
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $organizationId = $this->getUser()->getOrganizations()->getId();
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'organizationId' => $organizationId
        );
    }

    /**
     * Finds and displays a Users entity.
     *
     * @param integer $id
     *
     * @Route("/{id}", name="users_show")
     * @Method("GET")
     * @Template()
     *
     * @return array
     *
     */
    public function showAction($id) {
        $session = $this->getRequest()->getSession();
        if (($session->has('termsStatus') && $session->get('termsStatus') == 0) || ($session->has('limitExceed') && $session->get('limitExceed') == 0)) {
            return $this->redirect($this->generateUrl('dashboard'));
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationFrontBundle:Users')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Users entity.
     *
     * @param integer $id user id
     *
     * @Route("/{id}/edit", name="users_edit")
     * @Method("GET")
     * @Template()
     *
     * @return array
     */
    public function editAction($id) {
        @set_time_limit(0);
        @ini_set("memory_limit", "1000M"); # 1GB
        @ini_set("max_execution_time", 0); # unlimited
        $session = $this->getRequest()->getSession();
        if (($session->has('termsStatus') && $session->get('termsStatus') == 0) || ($session->has('limitExceed') && $session->get('limitExceed') == 0)) {
            return $this->redirect($this->generateUrl('dashboard'));
        }
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationFrontBundle:Users')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $organizationId = ($user->getOrganizations()) ? $user->getOrganizations()->getId() : "";
        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'organizationId' => $organizationId,
        );
    }

    /**
     * Creates a form to edit a Users entity.
     *
     * @param Users $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Users $entity) {
        $formOptions = $this->getRoleHierarchy();
        $formOptions['userRole'] = $entity->getRoles();
        $formOptions['currentUser'] = $this->getUser();
        $form = $this->createForm(new UsersType($formOptions), $entity, array(
            'action' => $this->generateUrl('users_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Users entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer                                   $id
     *
     * @Route("/{id}", name="users_update")
     * @Method("PUT")
     * @Template("ApplicationFrontBundle:Users:edit.html.twig")
     *
     * @return type
     */
    public function updateAction(Request $request, $id) {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ApplicationFrontBundle:Users')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setUsersUpdated($user);

            if (in_array("ROLE_SUPER_ADMIN", $entity->getRoles())) {
                $entity->removeOrganizations();
            }

            if (in_array("ROLE_ADMIN", $entity->getRoles()) || in_array("ROLE_SUPER_ADMIN", $entity->getRoles())) {
                $record = $request->request->get('application_bundle_frontbundle_users');
                if (isset($record['userProjects'])) {
                    $userProjects = $record['userProjects'];
                    if (count($userProjects) > 0) {
                        foreach ($userProjects as $project) {
                            $project = $em->getRepository('ApplicationFrontBundle:Projects')->find($project);
                            $entity->removeUserProjects($project);
                        }
                    }
                }
            }

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'User updated succesfully.');

            return $this->redirect($this->generateUrl('users'));
        }
        $organizationId = ($user->getOrganizations()) ? $user->getOrganizations()->getId() : "";

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'organizationId' => $organizationId,
        );
    }

    /**
     * Deletes a Users entity.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer                                   $id
     *
     * @Route("/{id}", name="users_delete")
     * @Method("DELETE")
     *
     * @return redirect to user list page
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ApplicationFrontBundle:Users')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Users entity.');
            }

            $records = $em->getRepository('ApplicationFrontBundle:Records')->findBy(array('user' => $id));
            foreach ($records as $record) {
                $shpinxInfo = $this->container->getParameter('sphinx_param');
                $sphinxSearch = new SphinxSearch($em, $shpinxInfo, $record->getId(), $record->getMediaType()->getId());
                $sphinxSearch->delete();
            }
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'User deleted succesfully.');
        }

        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * Creates a form to delete a Users entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('users_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete', 'attr' => array('onclick' => "return confirm('All the records associated with this user will also delete. Are you sure you want to delete selected user?')")))
                        ->getForm();
    }

    /**
     * Get roles form defined hierarchy in security.yml
     *
     * @return roles array
     */
    private function getRoleHierarchy() {
        $rolesChoices = array();

        $roles = $this->container->getParameter('security.role_hierarchy.roles');

        foreach ($roles as $role => $inheritedRoles) {
            foreach ($inheritedRoles as $id => $inheritedRole) {
                if (!array_key_exists($inheritedRole, $rolesChoices)) {
                    $arrInheritedRoles = explode("_", ucfirst(strtolower(trim($inheritedRole))));
                    array_shift($arrInheritedRoles);
                    $rInRoles = implode(" ", $arrInheritedRoles);
                    $rolesChoices[$inheritedRole] = $rInRoles;
                }
            }

            if (!array_key_exists($role, $rolesChoices)) {
                $arrRoles = explode("_", ucfirst(strtolower(trim($role))));
                array_shift($arrRoles);

                $rrRoles = implode(" ", $arrRoles);
                $rolesChoices[$role] = $rrRoles;
            }
        }
        $roleOptions['role'] = $role;
        $roleOptions['roles'] = $rolesChoices;
//         echo '<pre>';
//        print_r($roleOptions);
//        exit;
        return $roleOptions;
    }

    /**
     * Displays a projects in dropdown.
     *
     * @param integer $orgId Organization id
     *
     * @Route("/getOrganizationProjects/{orgId}/", name="record_get_project")
     * @Method("GET")
     * @Template()
     * @return template
     */
    public function getOrganizationProjectsAction($orgId) {
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('ApplicationFrontBundle:Projects')->findBy(array('organization' => $orgId, 'status' => 1));

        return $this->render('ApplicationFrontBundle:Users:getProjects.html.php', array(
                    'projects' => $projects
        ));
    }

    /**
     * Active/Inactive User.
     *
     * @param integer $id User id
     * @param integer $status User status id
     * 
     * @Route("/changestatus/{id}/{status}", name="user_changestatus")
     * @Method("GET")
     * @Template()
     * @return redirection
     */
    public function changeStatusAction($id, $status) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ApplicationFrontBundle:Users')->find($id);
        if ($status == 1) {
            $user->setEnabled(0);
            $this->get('session')->getFlashBag()->add('success', 'User disabled succesfully.');
        } else {
            $user->setEnabled(1);
            $this->get('session')->getFlashBag()->add('success', 'User activated succesfully.');
        }
        $em->flush();
        return $this->redirect($this->generateUrl('users'));
    }

}
