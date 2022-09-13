import ListPost from './components/ListPost';
import ShowButton from './components/ShowButton';
import AddCategory from './components/AddCategory';
import EditCategory from './components/EditCategory';
import NewPost from './components/NewPost';
import GetListPostByCategoryId from './components/GetListPostByCategoryId';

const routes = [
  {
    path: '/',
    component: ListPost,
    name: 'index',
  },
  {
    path: '/show-button',
    component: ShowButton,
    name: 'showButton',
  },
  {
    path: '/category/add',
    component: AddCategory,
    name: 'addCategory',
  },
  {
    path: '/category/edit/:id',
    component: EditCategory,
    name: 'editCategory'
  },
  {
    path: '/post/add',
    component: NewPost,
    name: 'addPost'
  },
  {
    path: '/get-list-post/:id',
    component: GetListPostByCategoryId,
    name: 'getListPost'
  }
];

export default routes;