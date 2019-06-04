'use strict';

const e = React.createElement;

class HomeForm extends React.Component {
  constructor(props) {
    super(props);
    this.state = { registered: true };
  }

  render() {
    if (this.state.registered) {
      return (

        <form class="form-signin" id="sign-in" method="post" action="login">
          <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
          <label for="inputLogin" class="sr-only">Username or Email</label>
          <input type="text" id="inputLogin" name="inputLogin" class="form-control" placeholder="Username or Email" required autofocus />
          <label for="inputPassword" class="sr-only">Password</label>
          <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required />
          <button class="btn btn-lg btn-primary btn-block" type="submit" >Sign in</button>
          <p class="mt-5 mb-3 text-muted">&copy; {(new Date()).getFullYear()}</p>
        </form>
      );
    } else {
      return (
        <form class="form-signin" id="register" method="post" action="register">
          <h1 class="h3 mb-3 font-weight-normal">Please sign up</h1>
          <label for="email" class="sr-only">Email address</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required autofocus />
          <label for="username" class="sr-only">Username</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="Username" required />
          <label for="firstname" class="sr-only">First Name</label>
          <input type="text" id="firstname" name="firstname" class="form-control" placeholder="First Name" required />
          <label for="lastname" class="sr-only">Last Name</label>
          <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Last Name" required />
          <label for="dateofbirth" class="sr-only">Date of birth</label>
          <input type="date" id="dateofbirth" name="dateofbirth" class="form-control" placeholder="Date of birth" required />
          <label for="password" class="sr-only">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Password" required />
          <p>Already have an account? Please <a href="#sign-in" onClick={(e) => this.handleChangeForm(true, e)}>Log In</a></p>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
          <p class="mt-5 mb-3 text-muted">&copy; {(new Date()).getFullYear()}</p>
        </form>

      );
    }

  }
  handleChangeForm(dfd, e){
    e.preventDefault();
    this.setState( {
      registered: dfd
    })
  };

}

ReactDOM.render(e(HomeForm), document.querySelector('.hereForm'));
