<?xml version="1.0" encoding="utf-8"?>
<!-- $Revision: 320216 $ -->

<phpdoc:classref xml:id="class.yaf-controller-abstract" xmlns:phpdoc="http://php.net/ns/phpdoc" xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xi="http://www.w3.org/2001/XInclude">

 <title>The Yaf_Controller_Abstract class</title>
 <titleabbrev>Yaf_Controller_Abstract</titleabbrev>

 <partintro>

<!-- {{{ Yaf_Controller_Abstract intro -->
  <section xml:id="yaf-controller-abstract.intro">
   &reftitle.intro;
   <para>
    <classname>Yaf_Controller_Abstract</classname> 是Yaf的MVC体系的核心部分。
    MVC是指Model-View-Controller, 是一个用于分离应用逻辑和表现逻辑的设计模式。
   </para>
   <para>
    每个用户自定义controller都应当继承<classname>Yaf_Controller_Abstract</classname>。
   </para>
   <para>
    你会发现在你自己的controller中无法定义__construct方法。因此，<classname>Yaf_Controller_Abstract</classname>
    提供了一个魔术方法Yaf_Controller_Abstract::init()。
   </para>
   <para>
    如果在你自己的controller里面已经定义了一个init()方法，当你的controller被实例化的时候，它将被调用。
   </para>
   <para>
    Action可能需要参数，当一个请求来到的时候，在路由中如果请求的参数有相同名称的变量（例如：<methodname>Yaf_Request_Abstract::getParam</methodname>），
    Yaf将把他们传递给action方法（see <methodname>Yaf_Action_Abstract::execute</methodname>）。
   </para>
  </section>
<!-- }}} -->

  <section xml:id="yaf-controller-abstract.synopsis">
   &reftitle.classsynopsis;

<!-- {{{ Synopsis -->
   <classsynopsis>
    <ooclass><classname>Yaf_Controller_Abstract</classname></ooclass>

<!-- {{{ Class synopsis -->
    <classsynopsisinfo>
     <ooclass>
      <modifier>abstract</modifier>
      <classname>Yaf_Controller_Abstract</classname>
     </ooclass>
    </classsynopsisinfo>
<!-- }}} -->
    <classsynopsisinfo role="comment">&Properties;</classsynopsisinfo>
    <fieldsynopsis>
     <modifier>public</modifier>
     <varname linkend="yaf-controller-abstract.props.actions">actions</varname>
    </fieldsynopsis>
    <fieldsynopsis>
     <modifier>protected</modifier>
     <varname linkend="yaf-controller-abstract.props.module">_module</varname>
    </fieldsynopsis>
    <fieldsynopsis>
     <modifier>protected</modifier>
     <varname linkend="yaf-controller-abstract.props.name">_name</varname>
    </fieldsynopsis>
    <fieldsynopsis>
     <modifier>protected</modifier>
     <varname linkend="yaf-controller-abstract.props.request">_request</varname>
    </fieldsynopsis>
    <fieldsynopsis>
     <modifier>protected</modifier>
     <varname linkend="yaf-controller-abstract.props.response">_response</varname>
    </fieldsynopsis>
    <fieldsynopsis>
     <modifier>protected</modifier>
     <varname linkend="yaf-controller-abstract.props.invoke-args">_invoke_args</varname>
    </fieldsynopsis>
    <fieldsynopsis>
     <modifier>protected</modifier>
     <varname linkend="yaf-controller-abstract.props.view">_view</varname>
    </fieldsynopsis>

    
    <classsynopsisinfo role="comment">&Methods;</classsynopsisinfo>
    <xi:include xpointer="xmlns(db=http://docbook.org/ns/docbook) xpointer(id('class.yaf-controller-abstract')/db:refentry/db:refsect1[@role='description']/descendant::db:methodsynopsis[1])" />
   </classsynopsis>
<!-- }}} -->

  </section>

  
<!-- {{{ Yaf_Controller_Abstract properties -->
  <section xml:id="yaf-controller-abstract.props">
   &reftitle.properties;
   <variablelist>
    <varlistentry xml:id="yaf-controller-abstract.props.actions">
     <term><varname>actions</varname></term>
     <listitem>
      <para>
      你也可以通过使用这个值和 <classname>Yaf_Action_Abstract</classname> 在一个单独的PHP脚本中定义action函数。
      <example>
       <title>在一个独立的文件中定义action</title>
        <programlisting role="php">
<![CDATA[
<?php
class IndexController extends Yaf_Controller_Abstract {
    protected $actions = array(
        /** now dummyAction is defined in a separate file */
        "dummy" => "actions/Dummy_action.php",
    );

    /* action method may have arguments */
    public indexAction($name, $id) {
       assert($name == $this->getRequest()->getParam("name"));
       assert($id   == $this->_request->getParam("id"));
    }
}
?>
]]>
        </programlisting>
      </example>
      <example>
       <title>Dummy_action.php</title>
        <programlisting role="php">
<![CDATA[
<?php
class DummyAction extends Yaf_Action_Abstract {
    /* a action class shall define this method  as the entry point */
    public execute() {
    }
}
?>
]]>
        </programlisting>
      </example>
      </para>
     </listitem>
    </varlistentry>
    <varlistentry xml:id="yaf-controller-abstract.props.module">
     <term><varname>_module</varname></term>
     <listitem>
      <para>
        模块名
      </para>
     </listitem>
    </varlistentry>
    <varlistentry xml:id="yaf-controller-abstract.props.name">
     <term><varname>_name</varname></term>
     <listitem>
      <para></para>
     </listitem>
    </varlistentry>
    <varlistentry xml:id="yaf-controller-abstract.props.request">
     <term><varname>_request</varname></term>
     <listitem>
      <para>
      当前的请求实例
      </para>
     </listitem>
    </varlistentry>
    <varlistentry xml:id="yaf-controller-abstract.props.response">
     <term><varname>_response</varname></term>
     <listitem>
      <para></para>
     </listitem>
    </varlistentry>
    <varlistentry xml:id="yaf-controller-abstract.props.invoke-args">
     <term><varname>_invoke_args</varname></term>
     <listitem>
      <para></para>
     </listitem>
    </varlistentry>
    <varlistentry xml:id="yaf-controller-abstract.props.view">
     <term><varname>_view</varname></term>
     <listitem>
      <para>
      视图引擎
      </para>
     </listitem>
    </varlistentry>
   </variablelist>
  </section>
<!-- }}} -->


 </partintro>

 &reference.yaf.entities.yaf-controller-abstract;

</phpdoc:classref>

<!-- Keep this comment at the end of the file
Local variables:
mode: sgml
sgml-omittag:t
sgml-shorttag:t
sgml-minimize-attributes:nil
sgml-always-quote-attributes:t
sgml-indent-step:1
sgml-indent-data:t
indent-tabs-mode:nil
sgml-parent-document:nil
sgml-default-dtd-file:"~/.phpdoc/manual.ced"
sgml-exposed-tags:nil
sgml-local-catalogs:nil
sgml-local-ecat-files:nil
End:
vim600: syn=xml fen fdm=syntax fdl=2 si
vim: et tw=78 syn=sgml
vi: ts=1 sw=1
-->
