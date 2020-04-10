var ReactLoginForm = React.createClass({
    render: function() {

            var $errors = '';
            var $errorList = [];
            if( this.props.error.length > 0 ) {

            
                for(var i = 0; i < this.props.error.length; i++ ) {
                    $errorList.push(<li dangerouslySetInnerHTML={{__html: this.props.error[i] }}/>);  
                }
                $errors = <ul>{$errorList}</ul>;
                 
            }

            return (
                <form onSubmit={this.props.handleForm} name="reactLoginForm" className="react-login-form">
                    {$errors}
                    <p>
                        <label for="react-login-name">שם משתמש</label>
                        <input placeholder="הכנס שם משתמש" type="text" name="reactLoginName" id="react-login-name"   />
                    </p>
                    <p>
                    <label for="react-login-password">סיסמה</label>
                    <input placeholder="הכנס סיסמה" type="password" name="reactLoginPassword" id="react-login-password"   />
                    </p>
                    <button type="submit">התחבר</button>

                </form>
            );
         
    } 
        
});

var ReactUserData = React.createClass({
    render: function() {
         
            return (
            <div className="react-user-data">
             Hello, {this.props.user.display_name}.
             </div>
            );
         
    } 
        
    
});

var ReactLogin = React.createClass({
    getInitialState: function(){
        return {
            logged: 0,
            error: [],
            user: {}
        }
    },
    
    checkFields: function(){
        var order = this.props.order;
        var $username = '';
        var $password = '';
        if( reactLogins.length > 1 ) {
            $username = window.reactLoginForm[order].reactLoginName.value;
            $password = window.reactLoginForm[order].reactLoginPassword.value;
        } else {
            $username = window.reactLoginForm.reactLoginName.value;
            $password = window.reactLoginForm.reactLoginPassword.value;
        }
        
        var $currentErrors = [];

        if( $username == '' ) {
            $currentErrors.push( "יש להכניס שם משתמש" );
        }

        if( $password == '' ) {
            $currentErrors.push( "יש להכניס סיסמה" );
        }

         
        this.setState({error: $currentErrors});
        
        
    },
    handleForm: function(e){
        e.preventDefault(); 

        this.checkFields();
        var order = this.props.order; 
        if( this.state.error.length == 0 ) {
            // Request Data
            var data = {
                action: 'react_login_user',
                _wpnonce: wpReactLogin.nonce,
                username: '',
                password: ''
            }

            if( reactLogins.length > 1 ) {
                data.username = window.reactLoginForm[order].reactLoginName.value;
                data.password = window.reactLoginForm[order].reactLoginPassword.value;
            } else {
                data.username = window.reactLoginForm.reactLoginName.value;
                data.password = window.reactLoginForm.reactLoginPassword.value;
            }

            jQuery.ajax({
              url: wpReactLogin.ajax_url,
              dataType: 'json',
              method: 'POST',
              data: data,
              cache: false,
              success: function(data) {
                 
                if( ! data.success ) {
                    var $currentErrors = this.state.error; 
                    $currentErrors.push( data.message );

                    if( $currentErrors.length > 0 ) {

                        this.setState({error: $currentErrors});
                         
                    }
                } else {

                    location.reload();
                    //this.setState({logged: 1, user: data.user.data});
                    // for( var doms = 0; doms < reactLoginDoms.length; doms++ ) {
                    //     reactLoginDoms[ doms ].setState({logged: 1, user: data.user.data});
                    // }

                }

              }.bind(this),
              error: function(xhr, status, err) {

                alert(err.toString());
              }.bind(this)
            });
        }
        
    },
    componentDidMount: function() {

        jQuery.ajax({
          url: wpReactLogin.ajax_url,
          dataType: 'json',
          data: {action: 'react_check_if_logged'},
          cache: false,
          success: function(data) {
             
            if( data.success ) {
                this.setState({ logged: 1, user: data.user.data});
            }

          }.bind(this),
             error: function(xhr, status, err) {
          }.bind(this)
        });
      },
    render: function(){
        var $renderElement = <ReactLoginForm 
                            error={this.state.error} 
                            handleForm={this.handleForm} />;
        if( this.state.logged ) {
            $renderElement = <ReactUserData user={this.state.user} />;
        }
        return ( $renderElement  );
    }
});

// Get all login widgets
var reactLogins = document.getElementsByClassName("react_login");
var reactLoginDoms = [];
// For each login, create a new ReactLogin element
for(var logins = 0; logins < reactLogins.length; logins++ ) {
    var dom = ReactDOM.render(
        <ReactLogin order={logins} />,
        reactLogins[ logins ]
     ); 
} 
 