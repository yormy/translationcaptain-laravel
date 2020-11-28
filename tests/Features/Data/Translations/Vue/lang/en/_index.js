import moduleUser from '@modules/User/Resources/components/lang/en/_index';

import LangDefaults from './_default.json';
import validations from './validations.json';
import fields from './fields.json';

/*eslint-disable */
import profile from './profile.json';
import confirm_actions from './confirm_actions.json';
import multilingual_admin from './multilingual-admin.json';
import misc from './misc.json';
import billing from './billing.json';
/* eslint-enable */

// import firewall from './vendor/firewall/_index'

// import auth from './test/level/auth.json'
import actions from './actions.json';
// import auth from './auth.json'

export default {
  ...LangDefaults,
  fields,
  validations,
  profile,
  confirm_actions,
  'multilingual-admin': multilingual_admin,
  misc,
  billing,
  modules: {
    user: moduleUser,
  },
  actions,
  // auth,
  // test : {
  //   level : {
  //     auth
  //   }
  // },
  // vendor : {
  //   firewall
  // }
};
