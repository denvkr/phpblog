<?php

/* @Twig/Exception/exception_full.html.twig */
class __TwigTemplate_fd715824d2e813fe64205ebbf1b85b61623f595d1753a7d7d5fbc077f83bc65f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@Twig/layout.html.twig", "@Twig/Exception/exception_full.html.twig", 1);
        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Twig/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_69de46bd994a6f5893854bc5d6bf6f1402a7ff04ad2ab552160fc361539af624 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_69de46bd994a6f5893854bc5d6bf6f1402a7ff04ad2ab552160fc361539af624->enter($__internal_69de46bd994a6f5893854bc5d6bf6f1402a7ff04ad2ab552160fc361539af624_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@Twig/Exception/exception_full.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_69de46bd994a6f5893854bc5d6bf6f1402a7ff04ad2ab552160fc361539af624->leave($__internal_69de46bd994a6f5893854bc5d6bf6f1402a7ff04ad2ab552160fc361539af624_prof);

    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        $__internal_c0bad5ae082cb1eaeddf43cf52a9dc89321e50f3f998bff54d8d072aa9e58fb7 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_c0bad5ae082cb1eaeddf43cf52a9dc89321e50f3f998bff54d8d072aa9e58fb7->enter($__internal_c0bad5ae082cb1eaeddf43cf52a9dc89321e50f3f998bff54d8d072aa9e58fb7_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\HttpFoundationExtension')->generateAbsoluteUrl($this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("bundles/framework/css/exception.css")), "html", null, true);
        echo "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
";
        
        $__internal_c0bad5ae082cb1eaeddf43cf52a9dc89321e50f3f998bff54d8d072aa9e58fb7->leave($__internal_c0bad5ae082cb1eaeddf43cf52a9dc89321e50f3f998bff54d8d072aa9e58fb7_prof);

    }

    // line 7
    public function block_title($context, array $blocks = array())
    {
        $__internal_8a9bc2c94ce4e06c44bfcfb885b8bbb61136242cbd1661e783f43ea212dfd176 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_8a9bc2c94ce4e06c44bfcfb885b8bbb61136242cbd1661e783f43ea212dfd176->enter($__internal_8a9bc2c94ce4e06c44bfcfb885b8bbb61136242cbd1661e783f43ea212dfd176_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        // line 8
        echo "    ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["exception"]) ? $context["exception"] : null), "message", array()), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, (isset($context["status_code"]) ? $context["status_code"] : null), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["status_text"]) ? $context["status_text"] : null), "html", null, true);
        echo ")
";
        
        $__internal_8a9bc2c94ce4e06c44bfcfb885b8bbb61136242cbd1661e783f43ea212dfd176->leave($__internal_8a9bc2c94ce4e06c44bfcfb885b8bbb61136242cbd1661e783f43ea212dfd176_prof);

    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        $__internal_f7951a41c6071c87a48b807e4bd20f62800fbcb2b68a0ae4f70174936ee41c68 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_f7951a41c6071c87a48b807e4bd20f62800fbcb2b68a0ae4f70174936ee41c68->enter($__internal_f7951a41c6071c87a48b807e4bd20f62800fbcb2b68a0ae4f70174936ee41c68_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 12
        echo "    ";
        $this->loadTemplate("@Twig/Exception/exception.html.twig", "@Twig/Exception/exception_full.html.twig", 12)->display($context);
        
        $__internal_f7951a41c6071c87a48b807e4bd20f62800fbcb2b68a0ae4f70174936ee41c68->leave($__internal_f7951a41c6071c87a48b807e4bd20f62800fbcb2b68a0ae4f70174936ee41c68_prof);

    }

    public function getTemplateName()
    {
        return "@Twig/Exception/exception_full.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 12,  72 => 11,  58 => 8,  52 => 7,  42 => 4,  36 => 3,  11 => 1,);
    }

    public function getSource()
    {
        return "{% extends '@Twig/layout.html.twig' %}

{% block head %}
    <link href=\"{{ absolute_url(asset('bundles/framework/css/exception.css')) }}\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
{% endblock %}

{% block title %}
    {{ exception.message }} ({{ status_code }} {{ status_text }})
{% endblock %}

{% block body %}
    {% include '@Twig/Exception/exception.html.twig' %}
{% endblock %}
";
    }
}
